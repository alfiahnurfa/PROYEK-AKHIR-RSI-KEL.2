<?php

namespace App\Http\Controllers\Berita;

use App\Models\Berita;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LihatBeritaController extends Controller
{
    /**
     * [cite_start]Sesuai PSD-007 (ambilDaftarBerita) [cite: 8557-8559]
     */
    public function ambilDaftarBerita(Request $request)
    {
        $berita = Berita::where('status_publikasi_berita', 'Terbit')
                        ->orderBy('tanggal_publikasi', 'desc')
                        ->get();
        
        return response()->json($berita, 200);
    }

    public function ambilBeritaTerkini(Request $request)
    {
        $berita = Berita::where('status_publikasi_berita', 'Terbit')
                        ->orderBy('tanggal_publikasi', 'desc')
                        ->take(3)
                        ->get();
        
        return response()->json($berita, 200);
    }

    /**
     * [cite_start]Sesuai PSD-007 (ambilDetailBerita) [cite: 8557-8559]
     */
    public function ambilDetailBerita($id_berita)
    {
        $berita = Berita::where('id_berita', $id_berita)
                        ->where('status_publikasi_berita', 'Terbit')
                        ->first();

        if (!$berita) {
            return response()->json(['status' => 'error', 'message' => 'Berita tidak ditemukan'], 404);
        }
        
        return response()->json($berita, 200);
    }
}