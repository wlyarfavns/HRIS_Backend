<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // 1. Menampilkan Halaman "Lupa Kata Sandi"
    public function requestForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Memproses Pengiriman Email
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.exists' => 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.'
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => 'Tautan pemulihan kata sandi telah dikirim ke email Anda!'])
                    : back()->withErrors(['email' => 'Gagal mengirim tautan. Silakan coba lagi.']);
    }

    // 3. Menampilkan Halaman "Atur Ulang Kata Sandi" (dari link email)
    public function resetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }

    // 4. Memproses Perubahan Kata Sandi di Database
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed', // Harus cocok dengan password_confirmation
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', 'Kata sandi berhasil diubah! Silakan masuk dengan kata sandi baru.')
                    : back()->withErrors(['email' => 'Token kedaluwarsa atau email tidak cocok.']);
    }
}