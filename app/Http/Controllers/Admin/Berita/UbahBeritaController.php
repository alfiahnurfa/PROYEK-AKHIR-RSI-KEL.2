<?php

namespace App\Http\Controllers\Admin\Berita;

use App\Models\Berita;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // [Tambahan] Import Storage untuk hapus file lama

class UbahBeritaController extends Controller
{
    // Cek role Admin
    private function cekAdmin() {
        if (Auth::user()->role !== 'admin') {
            return false;
        }
        return true;
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
            'gambar_berita' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
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
}