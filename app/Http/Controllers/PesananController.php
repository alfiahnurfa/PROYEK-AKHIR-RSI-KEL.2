<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Untuk database transaction

class PesananController extends Controller
{
    /**
     * Proses "Checkout"
     * Mengubah status 'Keranjang' menjadi 'Menunggu Pembayaran'
     * [cite_start]Sesuai PSD-003 (buatPesanan) [cite: 8543-8545]
     */
    public function buatPesanan(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.id_produk' => 'required|integer|exists:produks,id_produk',
            'items.*.kuantitas_produk' => 'required|integer|min:1',
            'items.*.harga_produk_tersimpan' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            DB::beginTransaction();

            // 1️⃣ Buat pesanan baru
            $pesanan = Pesanan::create([
                'id_pembeli' => $user->id_pengguna,
                'status_pesanan' => 'menunggu',
                'alamat_pengiriman' => $user->alamat,
            ]);

            // 2️⃣ Loop semua produk dari FE (BUY NOW atau CART)
            foreach ($request->items as $item) {

                $produk = Produk::find($item['id_produk']);

                // Cek stok
                if ($produk->stok_produk < $item['kuantitas_produk']) {
                    throw new \Exception("Stok {$produk->nama_produk} tidak mencukupi.");
                }

                // Kurangi stok
                $produk->stok_produk -= $item['kuantitas_produk'];
                $produk->save();

                // Insert detail pesanan
                DetailPesanan::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $item['id_produk'],
                    'kuantitas_produk' => $item['kuantitas_produk'],
                    'harga_produk_tersimpan' => $item['harga_produk_tersimpan'],
                ]);
            }

            // 3️⃣ Tambah pembayaran
            $pesanan->pembayaran()->create([
                'status_pembayaran' => 'menunggu_pembayaran',
                'metode_pembayaran' => 'QRIS'
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Checkout berhasil',
                'id_pesanan' => $pesanan->id_pesanan,
                'data' => $pesanan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Mengambil daftar riwayat pesanan (bukan keranjang)
     * [cite_start]Sesuai PSD-005 (ambilDaftarPesananPembeli) [cite: 8550-8551, 8553]
     */
    public function ambilDaftarPesanan(Request $request)
    {
        $user = Auth::user();
        
        $excludeStatus = ['Checkout', 'Keranjang'];

        $daftarPesanan = Pesanan::where('id_pembeli', $user->id_pengguna)
                                ->whereNotIn('status_pesanan', $excludeStatus) // Ambil semua KECUALI keranjang
                                ->with('detailPesanans.produk', 'pembayaran')
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        return response()->json($daftarPesanan, 200);
    }

    public function ambilDetailPesanan($id)
    {
        $user = Auth::user();
        
        $daftarPesanan = Pesanan::where('id_pembeli', $user->id_pengguna)
                                ->where('id_pesanan', $id)
                                ->with('detailPesanans.produk', 'pembayaran')
                                ->orderBy('created_at', 'desc')
                                ->first();
        
        if (!$daftarPesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pesanan tidak ditemukan.'
            ], 404); // 404 Not Found
        }
        return response()->json($daftarPesanan, 200);
    }

    /**
     * Cek status satu pesanan spesifik
     * [cite_start]Sesuai PSD-005 (cekStatusPesanan) [cite: 8550-8551, 8553]
     */
    public function cekStatusPesanan(Request $request, $id_pesanan)
    {
        $user = Auth::user();
        
        $pesanan = Pesanan::where('id_pesanan', $id_pesanan)
                          ->where('id_pembeli', $user->id_pengguna)
                          ->first();
                          
        if (!$pesanan) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_pesanan' => $pesanan->id_pesanan,
                'status_pesanan' => $pesanan->status_pesanan,
                'status_pembayaran' => $pesanan->pembayaran ? $pesanan->pembayaran->status_pembayaran : 'N/A'
            ]
        ], 200);
    }
}