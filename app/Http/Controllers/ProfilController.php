<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // <-- PENTING: Untuk mengambil user yang login
use Illuminate\Support\Facades\Hash; // <-- PENTING: Untuk cek & buat password
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // <-- PENTING: Untuk validasi email unik

class ProfilController extends Controller
{
    /**
     * Mengambil data profil user yang sedang login.
     * [cite_start]Sesuai PSD-001 (ambilProfil) [cite: 8535-8536]
     */
    public function ambilProfil(Request $request)
    {
        // Cara 1: Menggunakan helper Auth
        // $user = Auth::user();
        
        // Cara 2: Menggunakan $request (lebih disarankan di controller)
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'user' => $user
        ], 200);
    }

    /**
     * Memperbarui data profil user yang sedang login.
     * [cite_start]Sesuai PSD-001 (perbaruiProfil) [cite: 8535, 8537]
     */
    public function perbaruiProfil(Request $request)
    {
        $user = $request->user(); // Dapatkan user yang sedang login

        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:100',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users')->ignore($user->id_pengguna, 'id_pengguna') // Cek unik, KECUALI id dia sendiri
            ],
            'nomor_telepon' => 'sometimes|required|string|max:15',
            'alamat' => 'sometimes|required|string',
            // 'foto_profil' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048' // Jika Anda mau handle upload foto
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $validator->errors()
            ], 400);
        }

        // 2. Update data
        // $request->only() hanya akan mengambil field yang ada di validasi
        $user->update($request->only('nama', 'email', 'nomor_telepon', 'alamat'));

        // 3. Kembalikan Respon
        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'user' => $user // Kirim data user yang sudah terupdate
        ], 200);
    }

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
        // Sesuai pseudo-code: VERIFIKASI Password Lama
        if (!Hash::check($request->password_lama, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password lama Anda tidak cocok.'
            ], 401); // 401 Unauthorized
        }

        // 3. Update password baru
        // Sesuai pseudo-code: HASH Password Baru
        $user->password = Hash::make($request->password_baru);
        $user->save(); // Method 'save()' digunakan karena kita hanya mengubah 1 field

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diubah.'
        ], 200);
    }
}