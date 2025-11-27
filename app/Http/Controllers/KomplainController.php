<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Komplain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KomplainController extends Controller
{
    /**
     * Mengajukan komplain untuk pesanan yang sudah selesai
     * [cite_start]Sesuai PSD-006 (ajukanKomplain) [cite: 8554-8556]
     */
    public function ajukanKomplain(Request $request, $id_pesanan)
    {
        $user = Auth::user();

        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'judul_komplain' => 'required|string|max:150',
            'deskripsi_komplain' => 'required|string',
            // 'bukti_komplain' => 'nullable|string', // Asumsi URL ke gambar/video
            'bukti_komplain' => 'required|image|mimes:jpg,png|max:10240', // Asumsi URL ke gambar/video
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        $path = null;
        if ($request->hasFile('bukti_komplain')) {
            $path = $request->file('bukti_komplain')->store('images/komplain', 'public');
        }

        // 2. Cek Pesanan
        $pesanan = Pesanan::where('id_pesanan', $id_pesanan)
                          ->where('id_pembeli', $user->id_pengguna)
                          ->first();

        if (!$pesanan) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        // 3. Cek Precondition (Sesuai PSD-006: Pesanan harus "Selesai")
        if ($pesanan->status_pesanan !== 'selesai') {
            return response()->json([
                'status' => 'error', 
                'message' => 'Komplain hanya bisa diajukan untuk pesanan yang sudah Selesai.'
            ], 400);
        }
        
        // 4. Cek apakah sudah pernah komplain
        $existingKomplain = Komplain::where('id_pesanan', $id_pesanan)->first();
        if ($existingKomplain) {
            return response()->json(['status' => 'error', 'message' => 'Anda sudah mengajukan komplain untuk pesanan ini.'], 400);
        }

        // 5. Buat Komplain
        $komplain = Komplain::create([
            'id_pesanan' => $id_pesanan,
            'id_pembeli' => $user->id_pengguna,
            'judul_komplain' => $request->judul_komplain,
            'deskripsi_komplain' => $request->deskripsi_komplain,
            'bukti_komplain' => $path,
            'tanggal_pengajuan' => now(), // Sesuai pseudo-code
            'status_komplain' => 'Baru', // Sesuai pseudo-code
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Komplain berhasil diajukan.',
            'data' => $komplain
        ], 201);
    }
}