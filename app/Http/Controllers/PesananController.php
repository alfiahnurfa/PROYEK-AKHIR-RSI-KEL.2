<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Produk;
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

        // 1. Cari keranjang aktif
        $keranjang = Pesanan::where('id_pembeli', $user->id_pengguna)
                            ->where('status_pesanan', 'Keranjang')
                            ->with('detailPesanans') // Ambil detail item
                            ->first();

        if (!$keranjang || $keranjang->detailPesanans->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Keranjang Anda kosong'], 400);
        }

        // 2. Validasi input checkout
        $validator = Validator::make($request->all(), [
            'alamat_pengiriman' => 'required|string',
            'metode_pembayaran' => 'required|string|in:QRIS,VA,COD', // Contoh
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        // 3. Gunakan DB Transaction (PENTING!)
        // Ini untuk memastikan jika ada 1 produk gagal, semua proses dibatalkan.
        try {
            DB::beginTransaction();

            // 4. Cek stok semua item di keranjang
            foreach ($keranjang->detailPesanans as $item) {
                $produk = Produk::find($item->id_produk);
                if ($produk->stok_produk < $item->kuantitas_produk) {
                    throw new \Exception('Stok untuk produk ' . $produk->nama_produk . ' tidak mencukupi.');
                }
                // Kurangi stok
                $produk->stok_produk -= $item->kuantitas_produk;
                $produk->save();
            }
            
            // 5. Buat data Pembayaran
            $keranjang->pembayaran()->create([
                'status_pembayaran' => 'Menunggu',
                'metode_pembayaran' => $request->metode_pembayaran,
            ]);

            // 6. Update status Pesanan (dari 'Keranjang' menjadi 'Menunggu Pembayaran')
            $keranjang->update([
                'status_pesanan' => 'Menunggu Pembayaran',
                'alamat_pengiriman' => $request->alamat_pengiriman,
            ]);
            
            DB::commit(); // Semua sukses, simpan perubahan ke database

            return response()->json([
                'status' => 'success',
                'message' => 'Checkout berhasil, pesanan sedang diproses',
                'data' => $keranjang
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Ada error, batalkan semua perubahan
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
        
        $daftarPesanan = Pesanan::where('id_pembeli', $user->id_pengguna)
                                ->where('status_pesanan', '!=', 'Keranjang') // Ambil semua KECUALI keranjang
                                ->with('detailPesanans.produk', 'pembayaran')
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);
        
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