<?php

namespace App\Http\Controllers\Admin\Komplain;

use App\Models\Komplain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LihatKomplainController extends Controller
{
    private function cekAdmin() {
        if (Auth::user()->role !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * [cite_start]Sesuai PSD-010 (ambilDaftarKomplain) [cite: 8568-8570]
     */
    public function ambilDaftarKomplain(Request $request)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        $query = Komplain::with('pembeli', 'pesanan');
        
        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status_komplain', $request->status);
        }
        
        $komplains = $query->orderBy('tanggal_pengajuan', 'desc')->get();
        return response()->json($komplains, 200);
    }

    /**
     * [cite_start]Sesuai PSD-010 (ambilDetailKomplain) [cite: 8568-8570]
     */
    public function ambilDetailKomplain($id_komplain)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        $komplain = Komplain::with('pembeli', 'pesanan.detailPesanans.produk')->find($id_komplain);
        if (!$komplain) {
            return response()->json(['message' => 'Komplain tidak ditemukan'], 404);
        }
        
        return response()->json($komplain, 200);
    }
}