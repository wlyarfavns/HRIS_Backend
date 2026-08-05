<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\EmployeeContractController;
use App\Http\Controllers\AttendanceController;

Route::prefix('/v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/contracts/reminder-h30', [EmployeeContractController::class, 'reminderH30']);
        Route::apiResource('contracts', EmployeeContractController::class);
        
        // Endpoint Status Presensi (No 27)
        Route::post('/attendance/status', [AttendanceController::class, 'checkAttendanceStatus']);
    });
});