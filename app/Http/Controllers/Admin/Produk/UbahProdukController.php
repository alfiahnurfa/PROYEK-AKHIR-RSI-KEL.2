<?php

namespace App\Http\Controllers\Admin\Produk;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UbahProdukController extends Controller
{
    private function cekAdmin() {
        if (Auth::user()->role !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * Memperbarui produk yang ada.
     * [cite_start]Sesuai PSD-008 (ubahProduk) [cite: 8560, 8563]
     */
    public function ubahProduk(Request $request, $id)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        // 1. Cari produk
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // 2. Validasi
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'sometimes|required|string|max:100',
            'kategori_produk' => 'sometimes|required|string|max:50',
            'harga_produk' => 'sometimes|required|numeric|min:0',
            'stok_produk' => 'sometimes|required|integer|min:0',
            'deskripsi_produk' => 'nullable|string',
            'berat_produk' => 'sometimes|required|numeric|min:0',
            'status_produk' => 'sometimes|required|in:Aktif,Arsip',
            'foto_produk' => 'sometimes|image|mimes:jpeg,png,jpg|max:10240', 
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        $dataUpdate = $request->except(['foto_produk']);

        // 3. Cek Logika Upload Foto Baru
        if ($request->hasFile('foto_produk')) {
            
            // A. Hapus foto lama fisik (Gunakan Storage disk 'public')
            if ($produk->foto_produk && Storage::disk('public')->exists($produk->foto_produk)) {
                Storage::disk('public')->delete($produk->foto_produk);
            }

            // B. Simpan foto baru langsung ke disk 'public'
            $path = $request->file('foto_produk')->store('images/produk', 'public');
            
            // C. Masukkan path baru ke array dataUpdate
            $dataUpdate['foto_produk'] = $path;
        }

        // 4. Eksekusi Update
        // Sekarang $dataUpdate hanya berisi foto_produk JIKA ada file baru.
        // Jika tidak ada file baru, kolom foto_produk tidak disentuh sama sekali.
        $produk->update($dataUpdate);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil diperbarui',
            'produk' => $produk
        ], 200);
    }
}