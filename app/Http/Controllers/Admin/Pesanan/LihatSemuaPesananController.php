<?php

namespace App\Http\Controllers\Admin\Pesanan;

use App\Models\Pesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LihatSemuaPesananController extends Controller
{
    // Pastikan semua method di sini dicek role admin-nya
    private function cekAdmin() {
        if (Auth::user()->role !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * Admin mengambil semua pesanan (bukan keranjang)
     */
    public function ambilSemuaPesanan(Request $request)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        $daftarPesanan = Pesanan::where('status_pesanan', '!=', 'Keranjang')
                                ->with('pembeli', 'pembayaran', 'detailPesanans.produk')
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
        
        return response()->json($daftarPesanan, 200);
    }
}