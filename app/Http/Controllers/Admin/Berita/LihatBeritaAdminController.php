<?php

namespace App\Http\Controllers\Admin\Berita;

use App\Models\Berita;
use App\Http\Controllers\Controller;

class LihatBeritaController extends Controller
{
    /**
     * [cite_start]Sesuai PSD-007 (ambilDaftarBerita) [cite: 8557-8559]
     */
    public function ambilDaftarBerita()
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        $berita = Berita::orderBy('tanggal_publikasi', 'desc')
                        ->get();
        
        return response()->json($berita, 200);
    }
}