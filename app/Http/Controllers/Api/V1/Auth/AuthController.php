<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // 1. Cek Kredensial
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // 2. Validasi Role (Opsional tapi direkomendasikan)
        // Pastikan yang login di aplikasi mobile benar-benar karyawan
        if (!$user->hasRole('employee')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Aplikasi mobile khusus untuk karyawan.'
            ], 403);
        }

        // 3. Generate Token Sanctum
        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'data'    => [
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'company_id' => $user->company_id,
                ]
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }
}