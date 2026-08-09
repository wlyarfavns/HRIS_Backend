<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::user();

            // 1. Penanganan Login untuk API / Mobile (Jangan diubah)
            if ($request->expectsJson()) {

                $token = $user->createToken('company-token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil.',
                    'data' => [
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                        'user' => $user,
                        'company' => $user->company,
                    ]
                ]);
            }

            // 2. Penanganan Login 
            if ($user->hasRole('company')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('hr')) {
                return redirect()->route('hr.dashboard'); 
            } elseif ($user->hasRole('finance')) {
                return redirect()->route('finance.dashboard'); 
            } elseif ($user->hasRole('supervisor')) {
                return redirect()->route('supervisor.dashboard'); 
            }

            // Fallback default jika role tidak memiliki dashboard spesifik
            return redirect('/dashboard');
        }

        // Jika Login Gagal (API)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // Jika Login Gagal (WEB)
        return back()
            ->withErrors([
                'email' => 'Email atau password salah.'
            ])
            ->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        // Hapus token API jika ada
        if ($request->user() && method_exists($request->user(), 'currentAccessToken') && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        Auth::logout();

        // Hapus session Web
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Response untuk API
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.'
            ], 200);
        }

        // Response untuk Web
        return redirect('/');
    }

    public function create()
    {
        return view('auth.login');
    }
}