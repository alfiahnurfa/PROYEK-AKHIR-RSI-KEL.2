<?php

namespace App\Http\Controllers\Admin\Pesanan;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UbahPesananController extends Controller
{
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
            'status_pesanan' => 'required|string|in:dikemas,dikirim,selesai'
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
        if ($status_baru == 'dikemas' && $pesanan->pembayaran->status_pembayaran == 'menunggu_konfirmasi') {
            $pesanan->pembayaran->update(['status_pembayaran' => 'selesai']);
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