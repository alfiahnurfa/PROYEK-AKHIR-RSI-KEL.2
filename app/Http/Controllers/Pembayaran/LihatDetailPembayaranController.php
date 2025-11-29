<?php

namespace App\Http\Controllers\Pembayaran;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LihatDetailPembayaranController extends Controller
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
}