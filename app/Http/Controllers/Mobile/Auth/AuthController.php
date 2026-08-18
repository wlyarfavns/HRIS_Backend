<?php

namespace App\Http\Controllers\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use App\Mail\ActivationOtpMail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('nip', $request->nip)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'NIP atau password salah.'
            ], 401);
        }

        if (!$user->hasRole('employee')) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Aplikasi mobile khusus untuk karyawan.'
            ], 403);
        }

        $token = $user->createToken('mobile-token')->plainTextToken;

        $employee = $user->employee;

        if ($employee && in_array($employee->status, ['pending', 'pending_activation'])) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil. Silakan masukkan email Anda untuk mengaktifkan akun.',
                'needs_activation' => true,
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ], 200);
        }

        $user->load('employee.company');

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'needs_activation' => false,
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user 
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


    public function sendActivationOtp(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        if (!$employee || !in_array($employee->status, ['pending', 'pending_activation'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini sudah aktif atau data karyawan tidak ditemukan.'
            ], 400);
        }

        $request->validate([

            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->where('company_id', $user->company_id),
            ],
            'phone' => 'nullable|string|max:20',
        ]);


        $existingOtp = EmailOtp::where('user_id', $user->id)->latest()->first();

        if ($existingOtp && now()->lessThan($existingOtp->expires_at)) {
            $remaining = now()->diffInSeconds($existingOtp->expires_at);
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP masih aktif. Silakan tunggu ' . ceil($remaining / 60) . ' menit lagi untuk meminta kode baru.'
            ], 429); 
        }


        $otpCode = (string) random_int(100000, 999999);

        DB::transaction(function () use ($user, $request, $otpCode) {

            EmailOtp::where('user_id', $user->id)->delete();

            EmailOtp::create([
                'user_id' => $user->id,
                'email' => $request->email,
                'otp_code' => Hash::make($otpCode),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(5),
            ]);
        });

        Mail::to($request->email)->send(new ActivationOtpMail($otpCode));

        return response()->json([
            'success' => true,
            'message' => 'Kode verifikasi telah dikirim ke email Anda.',
        ], 200);
    }


    public function verifyActivationOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6',
        ]);

        $user = $request->user();

        $otp = EmailOtp::where('user_id', $user->id)
            ->where('email', $request->email)
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi tidak ditemukan. Silakan minta kode baru.'
            ], 404);
        }

        if ($otp->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode verifikasi sudah kedaluwarsa. Silakan minta kode baru.'
            ], 422);
        }

        if ($otp->attempts >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan salah. Silakan minta kode baru.'
            ], 429);
        }

        if (!Hash::check($request->otp_code, $otp->otp_code)) {
            $otp->increment('attempts');
            return response()->json([
                'success' => false,
                'message' => 'Kode tidak valid, silakan coba lagi.'
            ], 422);
        }

        $employee = $user->employee;

        if (!$employee || !in_array($employee->status, ['pending', 'pending_activation'])) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini sudah aktif atau data karyawan tidak ditemukan.'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $user->update(['email' => $otp->email]);

            $employee->update([
                'email' => $otp->email,
                'status' => 'active',
            ]);

            $otp->update(['verified_at' => now()]);

            $otp->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Aktivasi berhasil! Akun Anda kini sudah aktif sepenuhnya.',
                'data' => [
                    'id' => $user->id,
                    'nip' => $user->nip,
                    'email' => $user->email,
                    'name' => $user->name,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan email.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}