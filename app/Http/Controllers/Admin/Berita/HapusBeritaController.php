<?php

namespace App\Http\Controllers\Admin\Berita;

use App\Models\Berita;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // [Tambahan] Import Storage untuk hapus file lama

class HapusBeritaController extends Controller
{
    // Cek role Admin
    private function cekAdmin() {
        if (Auth::user()->role !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * Sesuai PSD-011 (hapusBerita)
     */
    public function hapusBerita($id_berita)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        $berita = Berita::find($id_berita);
        if (!$berita) {
            return response()->json(['message' => 'Berita tidak ditemukan'], 404);
        }

        // [Tambahan] Hapus file fisik gambar saat data dihapus
        if ($berita->gambar_berita && Storage::disk('public')->exists($berita->gambar_berita)) {
            Storage::disk('public')->delete($berita->gambar_berita);
        }

        $berita->delete();
        return response()->json(['message' => 'Berita berhasil dihapus'], 200);
    }
}