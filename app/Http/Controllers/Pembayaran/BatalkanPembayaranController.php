<?php

namespace App\Http\Controllers\Pembayaran;

use App\Models\Produk; 
use App\Models\Pesanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class BatalkanPembayaranController extends Controller
{
    /**
     * Pembeli membatalkan pesanan (sebelum dibayar)
     * Sesuai PSD-004 (batalkanPesanan)
     */
    public function batalkanPesanan(Request $request, $id_pesanan)
    {
        $user = Auth::user();
        $pesanan = Pesanan::where('id_pesanan', $id_pesanan)
                         ->where('id_pembeli', $user->id_pengguna)
                         ->with('detailPesanans', 'pembayaran') // Ambil item & pembayaran
                         ->first();

        if (!$pesanan) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        // Hanya boleh batal jika status 'Menunggu Pembayaran'
        if ($pesanan->status_pesanan !== 'menunggu') {
             return response()->json(['status' => 'error', 'message' => 'Pesanan yang sudah diproses tidak dapat dibatalkan.'], 400);
        }

        try {
            DB::beginTransaction();

            // 1. Kembalikan stok produk
            foreach ($pesanan->detailPesanans as $item) {
                // Gunakan increment untuk mengembalikan stok
                Produk::find($item->id_produk)->increment('stok_produk', $item->kuantitas_produk);
            }

            // 2. Update status pesanan & pembayaran
            $pesanan->update(['status_pesanan' => 'dibatalkan']);
            if ($pesanan->pembayaran) {
                $pesanan->pembayaran->update(['status_pembayaran' => 'dibatalkan']);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dibatalkan dan stok telah dikembalikan.'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membatalkan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}