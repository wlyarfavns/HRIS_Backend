<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\EmailOtp;
use App\Mail\ResetPasswordOtpMail;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{

    public function requestForm()
    {
        return view('auth.forgot-password');
    }


    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.exists' => 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.'
        ]);

        $user = User::where('email', $request->email)->first();


        $existingOtp = EmailOtp::where('user_id', $user->id)->first();
        if ($existingOtp && now()->lessThan($existingOtp->expires_at)) {
            session(['reset_email' => $user->email]);
            return redirect()->route('password.verify.form')->with('status', 'Kode OTP Anda masih aktif. Silakan cek email Anda untuk mendapatkan kode tersebut.');
        }

        $otpCode = rand(100000, 999999);

        EmailOtp::updateOrCreate(
            ['user_id' => $user->id, 'email' => $user->email],
            [
                'otp_code' => Hash::make($otpCode),
                'expires_at' => now()->addMinutes(15),
                'verified_at' => null,
                'reset_token' => null
            ]
        );

        Mail::to($user->email)->send(new ResetPasswordOtpMail($otpCode));


        session(['reset_email' => $user->email]);

        return redirect()->route('password.verify.form')->with('status', 'Kode OTP pemulihan kata sandi telah dikirim ke email Anda!');
    }


    public function verifyOtpForm()
    {
        if (!session('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.verify-otp', ['email' => session('reset_email')]);
    }


    public function processOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['otp_code' => 'Pengguna tidak ditemukan.'])->withInput();
        }

        $otpRecord = EmailOtp::where('user_id', $user->id)->first();

        if (!$otpRecord || now()->greaterThan($otpRecord->expires_at)) {
            return back()->withErrors(['otp_code' => 'Kode OTP kedaluwarsa. Silakan minta ulang.'])->withInput();
        }

        if (!Hash::check($request->otp_code, $otpRecord->otp_code)) {
            $otpRecord->increment('attempts');
            return back()->withErrors(['otp_code' => 'Kode OTP salah.'])->withInput();
        }


        $resetToken = Str::random(60);

        $otpRecord->update([
            'verified_at' => now(),
            'reset_token' => $resetToken
        ]);

        return redirect()->route('password.reset', ['token' => $resetToken, 'email' => $user->email]);
    }


    public function resetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email')
        ]);
    }


    public function updatePassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $otpRecord = EmailOtp::where('reset_token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$otpRecord || !$otpRecord->verified_at) {
            return back()->withErrors(['email' => 'Sesi reset kata sandi tidak valid atau kedaluwarsa.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password)
        ])->setRememberToken(Str::random(60));
        $user->save();


        $otpRecord->delete();
        session()->forget('reset_email');

        return redirect()->route('login')->with('success', 'Kata sandi berhasil diubah! Silakan masuk dengan kata sandi baru.');
    }
}