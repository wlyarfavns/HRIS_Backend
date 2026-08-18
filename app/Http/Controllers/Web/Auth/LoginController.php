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


            if ($user->hasRole('company')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('hr')) {
                return redirect()->route('hr.dashboard'); 
            } elseif ($user->hasRole('finance')) {
                return redirect()->route('finance.dashboard'); 
            } elseif ($user->hasRole('supervisor')) {
                return redirect()->route('supervisor.dashboard'); 
            }


            return redirect('/dashboard');
        }


        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }


        return back()
            ->withErrors([
                'email' => 'Email atau password salah.'
            ])
            ->onlyInput('email');
    }

    public function destroy(Request $request)
    {

        if ($request->user() && method_exists($request->user(), 'currentAccessToken') && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        Auth::logout();


        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }


        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil.'
            ], 200);
        }


        return redirect('/');
    }

    public function create()
    {
        return view('auth.login');
    }
}