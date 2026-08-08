<?php

namespace App\Http\Controllers\Mobile\Auth;

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
    // STEP 1: Cek NIP
    public function checkNip(Request $request)
    {
        $request->validate(['nip' => 'required|string']);

        // Cari user berdasarkan NIP (Asumsi NIP ada di tabel users)
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

    // STEP 2: Kirim OTP
    // STEP 2: Kirim OTP
    public function sendOtp(Request $request)
    {
        $request->validate(['nip' => 'required|string']);
        $user = User::where('nip', $request->nip)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }

        // --- TAMBAHKAN LOGIKA INI ---
        $existingOtp = EmailOtp::where('user_id', $user->id)->first();

        if ($existingOtp && now()->lessThan($existingOtp->expires_at)) {
            $remaining = now()->diffInSeconds($existingOtp->expires_at);
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP masih aktif. Silakan tunggu ' . ceil($remaining / 60) . ' menit lagi untuk mengirim ulang.'
            ], 429); 
        }
        // -----------------------------

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

    // STEP 3: Verifikasi OTP
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

        // Generate Token Rahasia untuk Step 4
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

    // STEP 4: Reset Password
// STEP 4: Reset Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'reset_token' => 'required|string',
            // Pastikan dari Flutter mengirim 'password' dan 'password_confirmation'
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()]
        ]);

        // 1. Cari record OTP yang memiliki reset_token tersebut
        $otpRecord = EmailOtp::where('reset_token', $request->reset_token)->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Token reset tidak valid atau sesi sudah kedaluwarsa.'
            ], 400);
        }

        // 2. Ambil data User berdasarkan user_id dari record OTP
        $user = User::find($otpRecord->user_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan.'
            ], 404);
        }

        // 3. Update password user
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // 4. Hapus record OTP agar reset_token ini tidak bisa dipakai ulang
        $otpRecord->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diperbarui. Silakan login dengan password baru.'
        ], 200);
    }
}