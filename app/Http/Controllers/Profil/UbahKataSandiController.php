<?php

namespace App\Http\Controllers\Profil;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UbahKataSandiController extends Controller
{
    /**
     * Mengubah kata sandi user yang sedang login.
     * [cite_start]Sesuai PSD-001 (ubahKataSandi) [cite: 8535, 8537]
     */
    public function ubahKataSandi(Request $request)
    {
        $user = $request->user();

        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'password_lama' => 'required|string',
            'password_baru' => 'required|string|min:8|confirmed', // 'confirmed' mewajibkan ada 'password_baru_confirmation'
        ]);

        if($validator->fails()){
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        // 2. Cek password lama
        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password lama Anda tidak cocok.'
            ], 401); // 401 Unauthorized
        }

        // 3. Update password baru
        $user->password = Hash::make($request->password_baru);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah.'
        ], 200);
    }
}