<?php

namespace App\Http\Controllers\Profil;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class UBahProfilController extends Controller
{
    /**
     * Memperbarui data profil user yang sedang login.
     * [cite_start]Sesuai PSD-001 (perbaruiProfil) [cite: 8535, 8537]
     */
    public function ubahProfil(Request $request)
    {
        $user = $request->user(); // Ambil user yang sedang login

        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'nama' => 'sometimes|required|string|max:100',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('users')->ignore($user->id_pengguna, 'id_pengguna')
            ],
            'nomor_telepon' => 'sometimes|required|string|max:15',
            'alamat' => 'sometimes|required|string',
            'foto_profil' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
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
        // $user->update($request->only('nama', 'email', 'nomor_telepon', 'alamat'));

        $data = $request->except(['foto_profil']);

        // 3. Cek Logika Upload Foto Baru
        if ($request->hasFile('foto_profil')) {
            
            // A. Hapus foto lama fisik (Gunakan Storage disk 'public')
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }

            // B. Simpan foto baru langsung ke disk 'public'
            $path = $request->file('foto_profil')->store('images/profil', 'public');
            
            // C. Masukkan path baru ke array data
            $data['foto_profil'] = $path;
        }

        // 4. Eksekusi Update
        // Sekarang $data hanya berisi foto_profil JIKA ada file baru.
        // Jika tidak ada file baru, kolom foto_profil tidak disentuh sama sekali.
        $user->update($data);


        // 3. Kembalikan Respon
        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'user' => $user // Kirim data user yang sudah terupdate
        ], 200);
    }
}