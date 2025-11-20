<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KeranjangController extends Controller
{
    /**
     * Dapatkan keranjang aktif milik user yang sedang login.
     * Sesuai PSD-003 (ambilDetailPesanan untuk keranjang)
     */
    public function ambilKeranjang(Request $request)
    {
        $user = Auth::user();

        // Cari pesanan yang statusnya 'Keranjang'
        $keranjang = Pesanan::where('id_pembeli', $user->id_pengguna)
            ->where('status_pesanan', 'Keranjang')
            ->with('detailPesanans.produk') // Ambil item & data produknya
            ->first();

        if (!$keranjang) {
            return response()->json([
                'status' => 'success',
                'message' => 'Keranjang Anda kosong',
                'data' => null
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'data' => $keranjang
        ], 200);
    }

    /**
     * Menambahkan item ke keranjang.
     * [cite_start]Sesuai PSD-003 (tambahItemKeranjang) [cite: 8543-8544]
     */
    public function tambahItem(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'id_produk' => 'required|integer|exists:produks,id_produk',
            'kuantitas' => 'required|integer|min:1',
            'mode' => 'in:cart,buy' // mode opsional: cart / buy
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        $mode = $request->mode ?? "cart"; // default keranjang

        // 1. Cek Produk & Stok
        $produk = Produk::find($request->id_produk);
        if ($produk->stok_produk < $request->kuantitas) {
            return response()->json(['status' => 'error', 'message' => 'Stok produk tidak mencukupi'], 400);
        }

        // =======================
        //          BUY NOW
        // =======================
        if ($mode === "buy") {

            // Selalu buat pesanan baru
            $pesanan = Pesanan::create([
                'id_pembeli' => $user->id_pengguna,
                'status_pesanan' => 'Checkout',
                'alamat_pengiriman' => $user->alamat,
            ]);

            $detail = DetailPesanan::create([
                'id_pesanan' => $pesanan->id_pesanan,
                'id_produk' => $request->id_produk,
                'kuantitas_produk' => $request->kuantitas,
                'harga_produk_tersimpan' => $produk->harga_produk,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Checkout langsung berhasil',
                'id_pesanan' => $pesanan->id_pesanan,
                'data' => $detail
            ], 201);
        }

        // =======================
        //        KERANJANG
        // =======================
        $keranjang = Pesanan::firstOrCreate(
            [
                'id_pembeli' => $user->id_pengguna,
                'status_pesanan' => 'Keranjang'
            ],
            [
                'alamat_pengiriman' => $user->alamat
            ]
        );

        // Cek apakah item sudah ada
        $item = DetailPesanan::where('id_pesanan', $keranjang->id_pesanan)
            ->where('id_produk', $request->id_produk)
            ->first();

        if ($item) {
            if ($produk->stok_produk < ($item->kuantitas_produk + $request->kuantitas)) {
                return response()->json(['status' => 'error', 'message' => 'Stok tidak cukup total kuantitas'], 400);
            }
            $item->kuantitas_produk += $request->kuantitas;
            $item->save();
        } else {
            $item = DetailPesanan::create([
                'id_pesanan' => $keranjang->id_pesanan,
                'id_produk' => $request->id_produk,
                'kuantitas_produk' => $request->kuantitas,
                'harga_produk_tersimpan' => $produk->harga_produk,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item ditambahkan ke keranjang',
            'id_pesanan' => $keranjang->id_pesanan,
            'data' => $item
        ], 201);
    }


    /**
     * Memperbarui kuantitas item di keranjang.
     * Sesuai PSD-003 (perbaruiKuantitas)
     */
    public function perbaruiKuantitas(Request $request, $id_detail)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'kuantitas' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        // 1. Cari item detail
        $item = DetailPesanan::find($id_detail);
        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Item tidak ditemukan'], 404);
        }

        // 2. Validasi Kepemilikan (PENTING!)
        // Pastikan item ini milik user yang sedang login dan masih di keranjang
        if ($item->pesanan->id_pembeli !== $user->id_pengguna || $item->pesanan->status_pesanan !== 'Keranjang') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403); // 403 Forbidden
        }

        // 3. Cek Stok
        $produk = Produk::find($item->id_produk);
        if ($produk->stok_produk < $request->kuantitas) {
            return response()->json(['status' => 'error', 'message' => 'Stok produk tidak mencukupi'], 400);
        }

        // 4. Update kuantitas
        $item->kuantitas_produk = $request->kuantitas;
        $item->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Kuantitas item diperbarui',
            'data' => $item
        ], 200);
    }

    /**
     * Menghapus item dari keranjang.
     * Sesuai PSD-003 (hapusItemKeranjang)
     */
    public function hapusItem($id_detail)
    {
        $user = Auth::user();

        $item = DetailPesanan::find($id_detail);
        if (!$item) {
            return response()->json(['status' => 'error', 'message' => 'Item tidak ditemukan'], 404);
        }

        // Validasi Kepemilikan (PENTING!)
        if ($item->pesanan->id_pembeli !== $user->id_pengguna || $item->pesanan->status_pesanan !== 'Keranjang') {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        }

        $item->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Item berhasil dihapus dari keranjang'
        ], 200);
    }
}
