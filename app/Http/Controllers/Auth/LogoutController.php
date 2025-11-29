<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LogoutController extends Controller
{
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

    public function me(Request $request) {
        return response()->json(Auth::user());
    }
}