<?php

namespace App\Http\Controllers\Pembayaran;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KonfirmasiPembayaranController extends Controller
{
    /**
     * (Simulasi) Pengguna mengonfirmasi bahwa mereka telah membayar.
     * Mengubah status dari 'Menunggu' -> 'Menunggu Konfirmasi'
     * Sesuai PSD-004 (perbaruiStatusPembayaran)
     */
    public function perbaruiStatusPembayaran(Request $request, $id_pesanan)
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
        
        if ($pembayaran->status_pembayaran !== 'menunggu_pembayaran') {
             return response()->json(['status' => 'error', 'message' => 'Pembayaran sudah dikonfirmasi atau dibatalkan'], 400);
        }

        // Ubah status
        $pembayaran->update([
            'status_pembayaran' => 'menunggu_konfirmasi',
            'tanggal_pembayaran' => now()
        ]);

        $pembayaran->pesanan->update([
            'status_pesanan' => 'menunggu_konfirmasi'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Konfirmasi pembayaran telah dikirim. Menunggu verifikasi admin.',
            'data' => $pembayaran
        ], 200);
    }
}