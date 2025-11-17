<?php

namespace App\Http\Controllers;

use App\Models\Produk; // Pastikan Anda use Model Produk
use App\Models\Pesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- PENTING: Untuk mengembalikan stok

class PembayaranController extends Controller
{
    /**
     * Mengambil detail pembayaran untuk satu pesanan
     * Sesuai PSD-004 (ambilDetailPembayaran)
     */
    public function ambilDetailPembayaran(Request $request, $id_pesanan)
    {
        $user = Auth::user();
        $pembayaran = Pembayaran::where('id_pesanan', $id_pesanan)
                                ->whereHas('pesanan', function($q) use ($user) {
                                    $q->where('id_pembeli', $user->id_pengguna);
                                })
                                ->first();

        if (!$pembayaran) {
            return response()->json(['status' => 'error', 'message' => 'Detail pembayaran tidak ditemukan'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $pembayaran], 200);
    }

    /**
     * (Simulasi) Pengguna mengonfirmasi bahwa mereka telah membayar.
     * Mengubah status dari 'Menunggu' -> 'Menunggu Konfirmasi'
     * Sesuai PSD-004 (perbaruiStatusPembayaran)
     */
    public function konfirmasiPembayaran(Request $request, $id_pesanan)
    {
        $user = Auth::user();
        $pembayaran = Pembayaran::where('id_pesanan', $id_pesanan)
                                ->whereHas('pesanan', function($q) use ($user) {
                                    $q->where('id_pembeli', $user->id_pengguna);
                                })
                                ->first();

        if (!$pembayaran) {
            return response()->json(['status' => 'error', 'message' => 'Pembayaran tidak ditemukan'], 404);
        }
        
        if ($pembayaran->status_pembayaran !== 'Menunggu') {
             return response()->json(['status' => 'error', 'message' => 'Pembayaran sudah dikonfirmasi atau dibatalkan'], 400);
        }

        // Ubah status
        $pembayaran->update([
            'status_pembayaran' => 'Menunggu Konfirmasi',
            'tanggal_pembayaran' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Konfirmasi pembayaran telah dikirim. Menunggu verifikasi admin.',
            'data' => $pembayaran
        ], 200);
    }

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
        if ($pesanan->status_pesanan !== 'Menunggu Pembayaran') {
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
            $pesanan->update(['status_pesanan' => 'Dibatalkan']);
            if ($pesanan->pembayaran) {
                $pesanan->pembayaran->update(['status_pembayaran' => 'Dibatalkan']);
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