<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function daftarPengguna(Request $request)
    {
        // 1. Validasi Input
        // Memastikan data yang dikirim frontend sesuai aturan
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nomor_telepon' => 'required|string|max:15',
            'alamat' => 'required|string',
        ]);

        // 2. Jika Validasi Gagal
        // Kirim respon error 400 (Bad Request)
        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dimasukkan tidak valid',
                'errors' => $validator->errors()
            ], 400);
        }

        // 3. Buat Pengguna Baru (Jika Validasi Berhasil)
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password), 
            'nomor_telepon' => $request->nomor_telepon,
            'alamat' => $request->alamat,
            'role' => 'pembeli' // default role
        ]);

        // 4. Buat Token API untuk Pengguna Baru
        $token = $user->createToken('auth_token_'. $user->nama)->plainTextToken;

        // 5. Kembalikan Respon Sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201); // 201 'Created'
    }

    public function me(Request $request) {
        return response()->json(Auth::user());
    }
}