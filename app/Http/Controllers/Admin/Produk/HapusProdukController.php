<?php

namespace App\Http\Controllers\Admin\Produk;

use App\Models\Produk;
use App\Http\Controllers\Controller;

class HapusProdukController extends Controller
{
    /**
     * Menghapus produk.
     * [cite_start]Sesuai PSD-008 (hapusProduk) [cite: 8560, 8563]
     */
    public function hapusProduk($id)
    {
        if (!$this->cekAdmin()) {
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