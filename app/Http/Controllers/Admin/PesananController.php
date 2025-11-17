<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pesanan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    // Pastikan semua method di sini dicek role admin-nya
    private function cekAdmin() {
        if (Auth::user()->role !== 'admin') {
            return false;
        }
        return true;
    }

    /**
     * Admin mengambil semua pesanan (bukan keranjang)
     */
    public function ambilSemuaPesanan(Request $request)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
        
        $daftarPesanan = Pesanan::where('status_pesanan', '!=', 'Keranjang')
                                ->with('pembeli', 'pembayaran', 'detailPesanans.produk')
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
        
        return response()->json($daftarPesanan, 200);
    }

    /**
     * Admin memperbarui status pesanan (Verifikasi bayar, kirim, selesai)
     * [cite_start]Sesuai PSD-009 (perbaruiStatusPesanan) [cite: 8564-8566]
     */
    public function perbaruiStatusPesanan(Request $request, $id_pesanan)
    {
        if (!$this->cekAdmin()) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $validator = Validator::make($request->all(), [
            // Pastikan status yang dikirim valid
            'status_pesanan' => 'required|string|in:Diproses,Dikirim,Selesai'
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        $pesanan = Pesanan::with('pembayaran')->find($id_pesanan);
        if (!$pesanan) {
            return response()->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }
        
        $status_baru = $request->status_pesanan;

        // Logika untuk konfirmasi pembayaran
        // Jika status baru 'Diproses', artinya admin memverifikasi pembayaran
        if ($status_baru == 'Diproses' && $pesanan->pembayaran->status_pembayaran == 'Menunggu Konfirmasi') {
            $pesanan->pembayaran->update(['status_pembayaran' => 'Selesai']);
        }
        
        // Update status pesanan
        $pesanan->update(['status_pesanan' => $status_baru]);
        
        // (Opsional: Kirim notifikasi ke user via email/dll)
        // Sesuai pseudo-code: CALL Sistem.KirimNotifikasi (ID_PEMBELI, STATUS_BARU)

        return response()->json([
            'status' => 'success',
            'message' => 'Status pesanan berhasil diperbarui',
            'data' => $pesanan
        ], 200);
    }
}