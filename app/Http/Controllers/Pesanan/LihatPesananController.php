<?php

namespace App\Http\Controllers\Pesanan;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LihatPesananController extends Controller
{
    /**
     * Mengambil daftar riwayat pesanan (bukan keranjang)
     * [cite_start]Sesuai PSD-005 (ambilDaftarPesananPembeli) [cite: 8550-8551, 8553]
     */
    public function ambilDaftarPesanan(Request $request)
    {
        $user = Auth::user();
        
        $excludeStatus = ['Checkout', 'Keranjang'];

        $daftarPesanan = Pesanan::where('id_pembeli', $user->id_pengguna)
                                ->whereNotIn('status_pesanan', $excludeStatus)
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
            ], 404);
        }
        return response()->json($daftarPesanan, 200);
    }
}