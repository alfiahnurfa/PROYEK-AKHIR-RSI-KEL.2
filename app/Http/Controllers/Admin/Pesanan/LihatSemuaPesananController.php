<?php

namespace App\Http\Controllers\Admin\Pesanan;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LihatSemuaPesananController extends Controller
{
    /**
     * Admin mengambil semua pesanan (bukan keranjang)
     */
    public function ambilSemuaPesanan()
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        $daftarPesanan = Pesanan::with('pembeli', 'pembayaran', 'detailPesanans.produk')
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
        
        return response()->json($daftarPesanan, 200);
    }
}