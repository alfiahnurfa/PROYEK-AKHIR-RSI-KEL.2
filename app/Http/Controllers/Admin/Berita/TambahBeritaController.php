<?php

namespace App\Http\Controllers\Admin\Berita;

use App\Models\Berita;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TambahBeritaController extends Controller
{
    /**
     * Sesuai PSD-011 (tambahBerita)
     */
    public function tambahBerita(Request $request)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // [Modifikasi] Validasi gambar diubah untuk menerima file image
        $validator = Validator::make($request->all(), [
            'judul_berita' => 'required|string|max:200',
            'isi_berita' => 'required|string',
            'gambar_berita' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240', // Max 2MB
            'status_publikasi_berita' => 'required|in:Draft,Terbit,Arsip',
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        // [Tambahan] Logika upload gambar
        $pathGambar = null;
        if ($request->hasFile('gambar_berita')) {
            // Simpan ke folder 'public/images/berita'
            $pathGambar = $request->file('gambar_berita')->store('images/berita', 'public');
        }
        
        $berita = Berita::create([
            'judul_berita' => $request->judul_berita,
            'isi_berita' => $request->isi_berita,
            'gambar_berita' => $pathGambar, // Simpan path gambar
            'status_publikasi_berita' => $request->status_publikasi_berita,
            'tanggal_publikasi' => now(), 
        ]);
        
        return response()->json($berita, 201);
    }
}