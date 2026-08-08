<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\Auth\AuthController;
use App\Http\Controllers\Mobile\Employee\AttendanceController; 
use App\Http\Controllers\Mobile\Employee\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes (Khusus Mobile)
|--------------------------------------------------------------------------
*/

// Endpoint Login Karyawan (Memerlukan NIP dan Password)
Route::post('/login', [AuthController::class, 'login']);

// Routes Lupa Password 
Route::post('/forgot-password/check-nip', [\App\Http\Controllers\Mobile\Auth\ForgotPasswordController::class, 'checkNip']);
Route::post('/forgot-password/send-otp', [\App\Http\Controllers\Mobile\Auth\ForgotPasswordController::class, 'sendOtp']);
Route::post('/forgot-password/verify-otp', [\App\Http\Controllers\Mobile\Auth\ForgotPasswordController::class, 'verifyOtp']);
Route::post('/forgot-password/reset', [\App\Http\Controllers\Mobile\Auth\ForgotPasswordController::class, 'resetPassword']);

// Endpoint yang membutuhkan Bearer Token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth & Aktivasi
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/activation/send-otp', [AuthController::class, 'sendActivationOtp']);
    Route::post('/activation/verify-otp', [AuthController::class, 'verifyActivationOtp']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Endpoint Absensi (Khusus Role Karyawan)
    Route::middleware('role:employee')->group(function () {
        Route::get('/attendances/today', [AttendanceController::class, 'today']); 
        Route::post('/attendances/check-in', [AttendanceController::class, 'checkIn']);
        Route::get('/attendances/summary', [AttendanceController::class, 'summary']);
        Route::post('/attendances/check-out', [AttendanceController::class, 'checkOut']); 
    });
    
});