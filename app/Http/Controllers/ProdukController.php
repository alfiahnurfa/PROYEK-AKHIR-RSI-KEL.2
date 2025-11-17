<?php

namespace App\Http\Controllers;

use App\Models\Produk; // <-- PENTING: Panggil Model Produk
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProdukController extends Controller
{
    /**
     * Menampilkan semua produk yang 'Aktif'.
     * [cite_start]Sesuai PSD-002 (ambilSemuaProduk) [cite: 8538-8540]
     */
    public function ambilSemuaProduk(Request $request)
    {
        // Menggunakan paginate() lebih baik dari all() agar data tidak meledak
        // Hanya ambil produk yang statusnya 'Aktif'
        $produks = Produk::where('status_produk', 'Aktif')->paginate(10);

        return response()->json($produks, 200);
    }

    /**
     * Menampilkan detail satu produk.
     * [cite_start]Sesuai PSD-002 (ambilDetailProduk) [cite: 8538-8539, 8541]
     */
    public function ambilDetailProduk($id)
    {
        // Cari produk berdasarkan ID dan statusnya
        $produk = Produk::where('id_produk', $id)
                        ->where('status_produk', 'Aktif')
                        ->first(); // Ambil 1 data

        // Jika produk tidak ditemukan (misal ID salah atau statusnya 'Arsip')
        if (!$produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan.'
            ], 404); // 404 Not Found
        }

        return response()->json($produk, 200);
    }
    
    /**
     * Mencari produk berdasarkan nama atau kategori.
     * [cite_start]Sesuai PSD-002 (cariProduk, filterProduk) [cite: 8538, 8540-8541]
     */
    public function cariProduk(Request $request)
    {
        // Buat query dasar
        $query = Produk::query()->where('status_produk', 'Aktif');

        // Jika ada parameter 'q' (query pencarian)
        if ($request->has('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nama_produk', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('deskripsi_produk', 'LIKE', '%' . $searchTerm . '%');
            });
        }
        
        // Jika ada parameter 'kategori'
        if ($request->has('kategori')) {
            $query->where('kategori_produk', $request->kategori);
        }

        // Jika ada parameter 'harga_min' dan 'harga_max'
        if ($request->has('harga_min') && $request->has('harga_max')) {
            $query->whereBetween('harga_produk', [$request->harga_min, $request->harga_max]);
        }

        $produks = $query->paginate(10);

        return response()->json($produks, 200);
    }
}