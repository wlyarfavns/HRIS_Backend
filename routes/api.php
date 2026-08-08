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