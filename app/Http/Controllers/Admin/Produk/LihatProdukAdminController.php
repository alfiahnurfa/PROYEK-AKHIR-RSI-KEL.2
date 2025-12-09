<?php

namespace App\Http\Controllers\Admin\Produk;

use App\Models\Produk;
use App\Http\Controllers\Controller;

class LihatProdukAdminController extends Controller
{
    public function ambilSemuaProduk()
    {
        if (!$this->cekAdmin()) {
                return response()->json(['message' => 'Akses ditolak'], 403);
        }
        // Hanya ambil produk yang statusnya 'Aktif'
        $produks = Produk::get();

        return response()->json($produks, 200);
    }
}
