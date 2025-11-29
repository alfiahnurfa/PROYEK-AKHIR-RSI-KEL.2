<?php

namespace App\Http\Controllers\Profil;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LihatProfilController extends Controller
{
    /**
     * Mengambil data profil user yang sedang login.
     * [cite_start]Sesuai PSD-001 (ambilProfil) [cite: 8535-8536]
     */
    public function ambilProfil(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'user' => $user
        ], 200);
    }
}