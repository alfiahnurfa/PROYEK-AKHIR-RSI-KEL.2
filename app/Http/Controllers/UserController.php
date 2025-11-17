<?php

// Tentukan namespace sesuai struktur folder Anda
namespace App\Http\Controllers;

use App\Models\User; // Panggil Model User
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // Panggil library Autentikasi
use Illuminate\Support\Facades\Hash; // Panggil library Hashing (untuk password)
use Illuminate\Support\Facades\Validator; // Panggil library Validator

class UserController extends Controller
{
    /**
     * Handle permintaan registrasi pengguna baru.
     */
    public function daftarPengguna(Request $request)
    {
        // 1. Validasi Input
        // Memastikan data yang dikirim frontend sesuai aturan
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' berarti harus ada field 'password_confirmation'
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
        // Sesuai pseudo-code: HASH Password DENGAN ALGORITMA SISTEM [cite: 6057]
        $user = User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password WAJIB di-hash!
            'nomor_telepon' => $request->nomor_telepon,
            'alamat' => $request->alamat,
            'role' => 'pembeli' // Sesuai rancangan, default role
        ]);

        // 4. Buat Token API (Sanctum)
        // Agar pengguna bisa langsung login setelah daftar
        $token = $user->createToken('auth_token_'. $user->nama)->plainTextToken;

        // 5. Kembalikan Respon Sukses
        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201); // 201 artinya 'Created'
    }

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
        // Sesuai pseudo-code: CALL user.readByEmail(Email) [cite: 6057]
        $user = User::where('email', $request->email)->first();

        // 3. Cek Password
        // Sesuai pseudo-code: VERIFIKASI Password [cite: 6057]
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

    /**
     * Handle permintaan logout pengguna.
     */
    public function logout(Request $request)
    {
        // Menggunakan 'auth:sanctum' middleware, kita bisa dapat user dari $request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ], 200);
    }
}