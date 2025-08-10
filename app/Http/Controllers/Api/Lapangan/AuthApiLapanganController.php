<?php

namespace App\Http\Controllers\Api\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthApiLapanganController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|alpha_dash',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)
            ->whereHas('role', function ($q) {
                $q->where('roleName', 'lapangan');
            })
            ->first();

        if (! $user) {
            Log::info('User not found', ['username' => $request->username]);
            return response()->json([
                'error' => 'User tidak ditemukan'
            ], 404);
        }

        if (! Hash::check($request->password, $user->password)) {
            Log::info('Password salah', ['username' => $request->username]);
            return response()->json([
                'error' => 'Kata sandi salah'
            ], 401);
        }

        // Generate token
        $token = $user->createToken('mobile-lapangan')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}

