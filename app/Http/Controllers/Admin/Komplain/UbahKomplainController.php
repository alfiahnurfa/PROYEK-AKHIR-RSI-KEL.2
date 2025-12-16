<?php

namespace App\Http\Controllers\Admin\Komplain;

use App\Models\Komplain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class UbahKomplainController extends Controller
{
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