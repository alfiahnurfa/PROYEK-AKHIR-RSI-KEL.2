<?php

namespace App\Http\Controllers\Admin;

use App\Models\Berita;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BeritaController extends Controller
{
    // Cek role Admin
    private function cekAdmin() {
        if (Auth::user()->role !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * [cite_start]Sesuai PSD-011 (tambahBerita) [cite: 8571-8573]
     */
    public function tambahBerita(Request $request)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'judul_berita' => 'required|string|max:200',
            'isi_berita' => 'required|string',
            'gambar_berita' => 'nullable|string|max:255',
            'status_publikasi_berita' => 'required|in:Draft,Terbit,Arsip',
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }
        
        $berita = Berita::create([
            'judul_berita' => $request->judul_berita,
            'isi_berita' => $request->isi_berita,
            'gambar_berita' => $request->gambar_berita,
            'status_publikasi_berita' => $request->status_publikasi_berita,
            'tanggal_publikasi' => now(), // Sesuai pseudo-code
        ]);
        
        return response()->json($berita, 201);
    }

    /**
     * [cite_start]Sesuai PSD-011 (ubahBerita) [cite: 8571-8572, 8574]
     */
    public function ubahBerita(Request $request, $id_berita)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        $berita = Berita::find($id_berita);
        if (!$berita) {
            return response()->json(['message' => 'Berita tidak ditemukan'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'judul_berita' => 'sometimes|required|string|max:200',
            'isi_berita' => 'sometimes|required|string',
            'status_publikasi_berita' => 'sometimes|required|in:Draft,Terbit,Arsip',
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }
        
        $berita->update($request->all());
        return response()->json($berita, 200);
    }

    /**
     * [cite_start]Sesuai PSD-011 (hapusBerita) [cite: 8571-8572, 8574]
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

        $berita->delete();
        return response()->json(['message' => 'Berita berhasil dihapus'], 200);
    }
}