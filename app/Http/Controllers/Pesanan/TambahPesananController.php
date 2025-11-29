<?php

namespace App\Http\Controllers\Pesanan;

use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 

class TambahPesananController extends Controller
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

            // 1. Buat pesanan baru
            $pesanan = Pesanan::create([
                'id_pembeli' => $user->id_pengguna,
                'status_pesanan' => 'menunggu',
                'alamat_pengiriman' => $user->alamat,
            ]);

            // 2️. Cek stok tiap produk & buat detail pesanan
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

            // 3️. Tambah pembayaran
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
}