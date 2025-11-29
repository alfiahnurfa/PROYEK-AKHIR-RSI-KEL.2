<?php

namespace App\Http\Controllers\Admin\Produk;

use App\Models\Produk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TambahProdukController extends Controller
{
    private function cekAdmin() {
        if (Auth::user()->role !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * Menambahkan produk baru.
     * [cite_start]Sesuai PSD-008 (tambahProduk) [cite: 8560-8561, 8563]
     */
    public function tambahProduk(Request $request)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'nama_produk' => 'required|string|max:100',
            'kategori_produk' => 'required|string|max:50',
            'deskripsi_produk' => 'nullable|string',
            'berat_produk' => 'required|numeric|min:0',
            'harga_produk' => 'required|numeric|min:0',
            'stok_produk' => 'required|integer|min:0',
            'foto_produk' => 'required|image|mimes:jpg,png|max:10240',
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }
        
        // Logika Upload File
        $path = null;
        if ($request->hasFile('foto_produk')) {
            $path = $request->file('foto_produk')->store('images/produk', 'public');
        }

        $produk = Produk::create([
            'nama_produk' => $request->nama_produk,
            'kategori_produk' => $request->kategori_produk,
            'deskripsi_produk' => $request->deskripsi_produk,
            'berat_produk' => $request->berat_produk,
            'harga_produk' => $request->harga_produk,
            'stok_produk' => $request->stok_produk,
            'foto_produk' => $path, 
            'status_produk' => 'Aktif',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan',
            'produk' => $produk
        ], 201);
    }
}