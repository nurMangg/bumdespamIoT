<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|alpha_dash',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->username)
            ->whereHas('role', function ($q) {
                $q->where('roleName', 'pelanggan');
            })
            ->first();

        if (! $user) {
            return response()->json([
                'error' => 'User tidak ditemukan'
            ], 404);
        }

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Kredensial yang diberikan salah.'
            ], 401);
        }
        
        $pelanggan = Pelanggan::where('pelangganUserId', $user->id)->first();

        // Generate token
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'pelanggan' => $pelanggan
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
    
    public function changePassword(Request $request)
    {
        $currentUser = $request->user();
        if (!$currentUser) {
            return response()->json(['error' => 'User not found'], 404);
        }
        
        $request->validate([
            'password' => 'required|min:6', // harus ada new_password_confirmation juga
        ]);
        
        User::find($currentUser)->first();
        
        $currentUser->password = Hash::make($request->password);
        $currentUser->save();
    
        return response()->json(['message' => 'Password berhasil diubah.'], 200);
        
        
        
    }
}
