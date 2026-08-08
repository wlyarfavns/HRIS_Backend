<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\Auth\AuthController;
use App\Http\Controllers\Mobile\Employee\AttendanceController; 
use App\Http\Controllers\Mobile\Employee\ProfileController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\EmployeeContractController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Api\V1\LeaveRequestController;

/*
|--------------------------------------------------------------------------
| API Routes (Khusus Mobile)
|--------------------------------------------------------------------------
*/

// Endpoint Login Karyawan (Memerlukan NIP dan Password)
Route::post('/login', [AuthController::class, 'login']);

Route::prefix('/v1')->group(function () {
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

        Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
        Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
        Route::patch('/leave-requests/{id}/approve', [LeaveRequestController::class, 'approve']);
        
        Route::apiResource('salary-components', \App\Http\Controllers\Api\SalaryComponentController::class);
        Route::post('/payroll/cutoff-attendance', [\App\Http\Controllers\Api\PayrollApiController::class, 'generateCutoff']);
        Route::post('/payroll/calculate', [\App\Http\Controllers\Api\PayrollApiController::class, 'calculateSalary']);

        Route::post('/payroll/{id}/approve-hr', [\App\Http\Controllers\Api\PayrollApiController::class, 'approveHr']);
        Route::post('/payroll/{id}/approve-finance', [\App\Http\Controllers\Api\PayrollApiController::class, 'approveFinance']);

        Route::post('/payroll/export-bank-csv', [\App\Http\Controllers\Api\PayrollApiController::class, 'exportBankCsv']);

        Route::get('/payroll/{id}/slip', [\App\Http\Controllers\Api\PayrollApiController::class, 'generateSlip']);
    });
    
});