<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\EmployeeContractController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Api\V1\LeaveRequestController;

Route::prefix('/v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/contracts/reminder-h30', [EmployeeContractController::class, 'reminderH30']);
        Route::apiResource('contracts', EmployeeContractController::class);
        
        Route::apiResource('departments', \App\Http\Controllers\Api\V1\DepartmentController::class);
        Route::apiResource('positions', \App\Http\Controllers\Api\V1\PositionController::class);
        Route::apiResource('job-grades', \App\Http\Controllers\Api\V1\JobGradeController::class);
        Route::apiResource('shifts', \App\Http\Controllers\Api\V1\ShiftController::class);

        Route::apiResource('roles', \App\Http\Controllers\Api\V1\RoleController::class);
        Route::apiResource('permissions', \App\Http\Controllers\Api\V1\PermissionController::class)->only(['index', 'show']);
        
        Route::post('/attendance/status', [AttendanceController::class, 'checkAttendanceStatus']);

        // Leave Requests (Cuti & Izin)
        Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
        Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
        Route::patch('/leave-requests/{id}/approve', [LeaveRequestController::class, 'approve']);
    });
});