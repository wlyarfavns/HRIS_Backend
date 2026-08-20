<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordOtpMail;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordController extends Controller
{

    public function checkNip(Request $request)
    {
        $request->validate(['nip' => 'required|string']);


        $user = User::where('nip', $request->nip)->first();

        if (!$user || !$user->email) {
            return response()->json([
                'success' => false,
                'message' => 'NIP tidak ditemukan atau email belum terdaftar.'
            ], 404);
        }

        $emailParts = explode('@', $user->email);
        $maskedEmail = substr($emailParts[0], 0, 3) . '***@' . $emailParts[1];

        return response()->json([
            'success' => true,
            'message' => 'NIP valid.',
            'data' => ['masked_email' => $maskedEmail]
        ], 200);
    }



    public function sendOtp(Request $request)
    {
        $request->validate(['nip' => 'required|string']);
        $user = User::where('nip', $request->nip)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }


        $existingOtp = EmailOtp::where('user_id', $user->id)->first();

        if ($existingOtp && now()->lessThan($existingOtp->expires_at)) {
            $remaining = now()->diffInSeconds($existingOtp->expires_at);
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP masih aktif. Silakan tunggu ' . ceil($remaining / 60) . ' menit lagi untuk mengirim ulang.'
            ], 429); 
        }

        $otpCode = rand(100000, 999999);

        EmailOtp::updateOrCreate(
            ['user_id' => $user->id, 'email' => $user->email],
            [
                'otp_code' => Hash::make($otpCode),
                'expires_at' => now()->addMinutes(5),
                'verified_at' => null,
                'reset_token' => null
            ]
        );

        Mail::to($user->email)->send(new ResetPasswordOtpMail($otpCode));

        return response()->json([
            'success' => true,
            'message' => 'OTP berhasil dikirim.'
        ], 200);
    }


    public function verifyOtp(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'otp_code' => 'required|string|size:6'
        ]);

        $user = User::where('nip', $request->nip)->first();
        $otpRecord = EmailOtp::where('user_id', $user->id)->first();

        if (!$otpRecord || now()->greaterThan($otpRecord->expires_at) || !Hash::check($request->otp_code, $otpRecord->otp_code)) {
            return response()->json(['success' => false, 'message' => 'Kode OTP tidak valid atau kedaluwarsa.'], 400);
        }


        $resetToken = Str::random(60);

        $otpRecord->update([
            'verified_at' => now(),
            'reset_token' => $resetToken
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP valid.',
            'data' => ['reset_token' => $resetToken]
        ], 200);
    }


    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string',

            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()]
        ]);


        $otpRecord = EmailOtp::where('reset_token', $request->reset_token)->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Token reset tidak valid atau sesi sudah kedaluwarsa.'
            ], 400);
        }


        $user = User::find($otpRecord->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan.'
            ], 404);
        }


        $user->update([
            'password' => Hash::make($request->password)
        ]);


        $otpRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui. Silakan login dengan password baru.'
        ], 200);
    }
}
