<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;

class AuthenticationController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken("user login")->plainTextToken;
    }

    public function register(Request $request) {
        $request->validate([
            'username' => 'required|string|unique:users,username|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);
    
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        return response()->json([
            'message' => 'User registered successfully',
        ], 201);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'username' => 'sometimes|required|string|unique:users,username|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . Auth::id(),
            'firstname' => 'sometimes|required|string|max:100',
            'lastname' => 'sometimes|required|string|max:100',
            'password' => 'sometimes|required',
        ]);

        $user = Auth::user();

        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('firstname')) {
            $user->firstname = $request->firstname;
        }
        if ($request->has('lastname')) {
            $user->lastname = $request->lastname;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user
        ], 200);
    }
    
    public function search($id)
    {
        $user = User::findOrFail($id);
        return new UserResource($user);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(["anda sudah logout"]);
    }

    public function me(Request $request) {
        return response()->json(Auth::user());
    }
}
