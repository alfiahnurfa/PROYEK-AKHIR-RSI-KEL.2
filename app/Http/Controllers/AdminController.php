<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Add a new user (event coordinator or any user)
    public function addUser(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,event_coordinator,user', // You can define allowed roles here
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role (for example, event_coordinator or admin)
        $user->assignRole($request->role); // Assuming you're using a package like Spatie's Role & Permission package

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully.',
            'user' => $user,
        ], 201);
    }

    public function removeUser($userId)
    {
        // Find the user
        $user = User::findOrFail($userId);

        // Delete the user
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully.',
        ], 200);
    }
}
