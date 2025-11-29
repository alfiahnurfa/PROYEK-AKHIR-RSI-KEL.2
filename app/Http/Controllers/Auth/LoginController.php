<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle permintaan login pengguna.
     * Sesuai dengan PSD-001 (masuk) [cite: 6056-6057]
     */
    public function masuk(Request $request)
    {
        // 1. Validasi Input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dimasukkan tidak valid',
                'errors' => $validator->errors()
            ], 400);
        }

        // 2. Cek Email
        $user = User::where('email', $request->email)->first();

        // 3. Cek Password
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Jika email tidak ada ATAU password salah
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau Password salah'
            ], 401); // 401 artinya 'Unauthorized'
        }

        // 4. Jika Berhasil, Buat Token API
        // Hapus token lama jika ada (opsional, tapi bagus untuk keamanan)
        $user->tokens()->delete();
        
        $token = $user->createToken('auth_token_'. $user->nama)->plainTextToken;

        // 5. Kembalikan Respon Sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user // Kirim data user juga
        ], 200); // 200 artinya 'OK'
    }

    public function me(Request $request) {
        return response()->json(Auth::user());
    }
}