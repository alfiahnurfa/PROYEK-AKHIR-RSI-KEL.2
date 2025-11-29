<?php

namespace App\Http\Controllers\Produk;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LihatProdukController extends Controller
{
    /**
     * Menampilkan semua produk yang 'Aktif'.
     * [cite_start]Sesuai PSD-002 (ambilSemuaProduk) [cite: 8538-8540]
     */
    public function ambilSemuaProduk(Request $request)
    {
        // Hanya ambil produk yang statusnya 'Aktif'
        $produks = Produk::where('status_produk', 'Aktif')->get();

        return response()->json($produks, 200);
    }

    public function ambilProdukTerlaris()
    {
        $produk = DB::table('produks')
            ->select(
                'produks.id_produk',
                'produks.nama_produk',
                'produks.harga_produk',
                'produks.deskripsi_produk',
                'produks.foto_produk',
                'produks.stok_produk',
                DB::raw('SUM(detail_pesanans.kuantitas_produk) as total_terjual')
            )
            ->join('detail_pesanans', 'detail_pesanans.id_produk', '=', 'produks.id_produk')
            ->join('pesanans', 'pesanans.id_pesanan', '=', 'detail_pesanans.id_pesanan')
            ->where('pesanans.status_pesanan', 'selesai')
            ->groupBy('produks.id_produk', 'produks.nama_produk', 'produks.harga_produk', 'produks.deskripsi_produk', 'produks.foto_produk', 'produks.stok_produk')
            ->orderBy('total_terjual', 'desc')
            ->limit(3)
            ->get();


        return response()->json($produk, 200);
    }

    /**
     * Menampilkan detail satu produk.
     * [cite_start]Sesuai PSD-002 (ambilDetailProduk) [cite: 8538-8539, 8541]
     */
    public function ambilDetailProduk($id)
    {
        $produk = Produk::where('id_produk', $id)
            ->where('status_produk', 'Aktif')
            ->first(); // Ambil 1 data

        if (!$produk) {
            return response()->json([
                'status' => 'error',
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        return response()->json($produk, 200);
    }

    /**
     * Mencari produk berdasarkan nama atau kategori.
     * [cite_start]Sesuai PSD-002 (cariProduk, filterProduk) [cite: 8538, 8540-8541]
     */
    public function cariProduk(Request $request)
    {
        $query = Produk::query()->where('status_produk', 'Aktif');

        if ($request->has('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_produk', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('deskripsi_produk', 'LIKE', '%' . $searchTerm . '%');
            });
        }

        if ($request->has('kategori')) {
            $query->where('kategori_produk', $request->kategori);
        }

        if ($request->has('harga_min') && $request->has('harga_max')) {
            $query->whereBetween('harga_produk', [$request->harga_min, $request->harga_max]);
        }

        $produks = $query->paginate(10);

        return response()->json($produks, 200);
    }
}
