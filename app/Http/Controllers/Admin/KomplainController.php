<?php

namespace App\Http\Controllers\Admin;

use App\Models\Komplain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KomplainController extends Controller
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
    
    /**
     * (Tambahan) Admin memperbarui status komplain
     */
    public function perbaruiStatusKomplain(Request $request, $id_komplain)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status_komplain' => 'required|string|in:Diproses,Selesai'
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }
        
        $komplain = Komplain::find($id_komplain);
        if (!$komplain) {
            return response()->json(['message' => 'Komplain tidak ditemukan'], 404);
        }

        $komplain->update(['status_komplain' => $request->status_komplain]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status komplain diperbarui',
            'data' => $komplain
        ], 200);
    }
}