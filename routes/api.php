<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\EmployeeContractController;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        
        Route::get('/contracts/reminder-h30', [EmployeeContractController::class, 'reminderH30']);
        Route::apiResource('contracts', EmployeeContractController::class);
    });
});