<?php

namespace App\Http\Controllers\Pembayaran;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class KonfirmasiPembayaranController extends Controller
{
    public function perbaruiStatusPembayaran(Request $request, $id_pesanan)
    {
        // 1: Inisialisasi dan Ambil Pengguna
        $user = Auth::user();

        // 2: Query Pembayaran
        $pembayaran = Pembayaran::where('id_pesanan', $id_pesanan)
                                ->whereHas('pesanan', function($q) use ($user) {
                                    // 3: Kondisi Kepemilikan (Closure)
                                    $q->where('id_pembeli', $user->id_pengguna);
                                })
                                ->first();

        // 4: Predicate Node 1: Pembayaran ditemukan?
        if (!$pembayaran) {
            // 5: Gagal Ditemukan (Akses Ditolak/404)
            return response()->json(['status' => 'error', 'message' => 'Pembayaran tidak ditemukan'], 404);
        }

        // 6: Predicate Node 2: Status pembayaran sudah dikonfirmasi/dibatalkan?
        if ($pembayaran->status_pembayaran !== 'menunggu_pembayaran') {
             // 7: Status Pembayaran Salah (400)
             return response()->json(['status' => 'error', 'message' => 'Pembayaran sudah dikonfirmasi atau dibatalkan'], 400);
        }

        // 8: Ubah status Pembayaran
        $pembayaran->update([
            'status_pembayaran' => 'menunggu_konfirmasi',
            'tanggal_pembayaran' => now()
        ]);

        // 9: Ubah status Pesanan
        $pembayaran->pesanan->update([
            'status_pesanan' => 'menunggu_konfirmasi'
        ]);

        // 10: Respons Sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Konfirmasi pembayaran telah dikirim. Menunggu verifikasi admin.',
            'data' => $pembayaran
        ], 200);
    }
}
