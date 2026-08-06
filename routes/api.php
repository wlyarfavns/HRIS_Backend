<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;

Route::prefix('v1')->group(function () {
    // Endpoint Terbuka (Login Mobile)
    Route::post('/login', [AuthController::class, 'login']);

    // Endpoint Tertutup (Harus bawa Token dari Flutter)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        
        //route presensi dll masuk di sini
        // Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    });
});