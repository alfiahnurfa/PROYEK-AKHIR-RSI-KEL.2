<?php

namespace App\Http\Controllers\Admin;

use App\Models\Berita;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // [Tambahan] Import Storage untuk hapus file lama

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
            'gambar_berita' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
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

    /**
     * Sesuai PSD-011 (ubahBerita)
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
        
        // [Modifikasi] Validasi gambar
        $validator = Validator::make($request->all(), [
            'judul_berita' => 'sometimes|required|string|max:200',
            'isi_berita' => 'sometimes|required|string',
            'gambar_berita' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status_publikasi_berita' => 'sometimes|required|in:Draft,Terbit,Arsip',
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        // Ambil semua data request
        $data = $request->except(['gambar_berita']);

        // [Tambahan] Cek apakah ada file gambar baru yang diupload
        if ($request->hasFile('gambar_berita')) {
            // Hapus gambar lama jika ada
            if ($berita->gambar_berita && Storage::disk('public')->exists($berita->gambar_berita)) {
                Storage::disk('public')->delete($berita->gambar_berita);
            }
            // Simpan gambar baru
            $data['gambar_berita'] = $request->file('gambar_berita')->store('images/berita', 'public');
        }
        
        $berita->update($data);
        return response()->json($berita, 200);
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