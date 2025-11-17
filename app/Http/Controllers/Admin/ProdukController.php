<?php

// PENTING: Namespace-nya ada 'Admin'
namespace App\Http\Controllers\Admin;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Support\Facades\Storage; // Gunakan ini jika Anda handle upload foto

class ProdukController extends Controller
{
    /**
     * (Opsional) Menampilkan semua produk untuk Admin (termasuk Arsip)
     */
    public function index()
    {
        // Cek Role Admin (Ini cara sederhana, pakai Spatie/RoleMiddleware lebih baik)
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        $produks = Produk::orderBy('created_at', 'desc')->paginate(10);
        return response()->json($produks, 200);
    }

    /**
     * Menambahkan produk baru.
     * [cite_start]Sesuai PSD-008 (tambahProduk) [cite: 8560-8561, 8563]
     */
    public function tambahProduk(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:100',
            'kategori_produk' => 'required|string|max:50',
            'deskripsi_produk' => 'nullable|string',
            'berat_produk' => 'required|numeric|min:0',
            'harga_produk' => 'required|numeric|min:0',
            'stok_produk' => 'required|integer|min:0',
            'foto_produk' => 'required|string', // Untuk sementara pakai URL string dulu
            // 'foto_produk' => 'required|image|mimes:jpg,png|max:2048', // Jika mau upload file
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }
        
        // --- (Logika Upload File jika diperlukan) ---
        // $path = null;
        // if ($request->hasFile('foto_produk')) {
        //     $path = $request->file('foto_produk')->store('public/produks');
        // }

        $produk = Produk::create([
            'nama_produk' => $request->nama_produk,
            'kategori_produk' => $request->kategori_produk,
            'deskripsi_produk' => $request->deskripsi_produk,
            'berat_produk' => $request->berat_produk,
            'harga_produk' => $request->harga_produk,
            'stok_produk' => $request->stok_produk,
            'foto_produk' => $request->foto_produk, // Ganti dengan $path jika upload file
            'status_produk' => 'Aktif', // Sesuai pseudo-code
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan',
            'produk' => $produk
        ], 201); // 201 Created
    }

    /**
     * Memperbarui produk yang ada.
     * [cite_start]Sesuai PSD-008 (ubahProduk) [cite: 8560, 8563]
     */
    public function ubahProduk(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        // 1. Cari produknya dulu
        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        // 2. Validasi data (opsional, tapi bagus)
        $validator = Validator::make($request->all(), [
            'nama_produk' => 'sometimes|required|string|max:100',
            'kategori_produk' => 'sometimes|required|string|max:50',
            'harga_produk' => 'sometimes|required|numeric|min:0',
            'stok_produk' => 'sometimes|required|integer|min:0',
            'status_produk' => 'sometimes|required|in:Aktif,Arsip', // Pastikan valuenya benar
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        // 3. Update produk
        $produk->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil diperbarui',
            'produk' => $produk
        ], 200);
    }

    /**
     * Menghapus produk.
     * [cite_start]Sesuai PSD-008 (hapusProduk) [cite: 8560, 8563]
     */
    public function hapusProduk($id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $produk = Produk::find($id);
        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $produk->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil dihapus'
        ], 200);
    }
}