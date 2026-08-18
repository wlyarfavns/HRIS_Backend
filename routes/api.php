<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Mobile\Auth\AuthController;
use App\Http\Controllers\Mobile\Auth\ForgotPasswordController;
use App\Http\Controllers\Mobile\Employee\AttendanceController;
use App\Http\Controllers\Mobile\Employee\ProfileController;
use App\Http\Controllers\Mobile\Employee\OvertimeController;
use App\Http\Controllers\Mobile\Employee\PayrollController;
use App\Http\Controllers\Mobile\Employee\ReimbursementController;
use App\Http\Controllers\Mobile\Employee\ShiftController as EmployeeShiftController;
use App\Http\Controllers\Mobile\Employee\NotificationController;

use App\Http\Controllers\Mobile\Admin\RoleController;
use App\Http\Controllers\Mobile\Admin\PayrollApiController;
use App\Http\Controllers\Mobile\Admin\JobGradeController;
use App\Http\Controllers\Mobile\Admin\PermissionController;
use App\Http\Controllers\Mobile\Employee\LeaveTypeController;

use App\Http\Controllers\Web\company\DepartmentController;
use App\Http\Controllers\Web\HR\EmployeeContractController;
use App\Http\Controllers\Web\HR\PositionController;
use App\Http\Controllers\Web\HR\ShiftController;
use App\Http\Controllers\Web\HR\LeaveRequestController;
use App\Http\Controllers\Web\HR\SalaryComponentWebController as SalaryComponentController;

Route::prefix('/mobile')->group(function () {


    Route::post('/login', [AuthController::class, 'login']);

    Route::post('/forgot-password/check-nip', [ForgotPasswordController::class, 'checkNip']);
    Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
    Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
    Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword']);


    Route::middleware('auth:sanctum')->group(function () {


        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/activation/send-otp', [AuthController::class, 'sendActivationOtp']);
        Route::post('/activation/verify-otp', [AuthController::class, 'verifyActivationOtp']);
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile/update', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);


        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);


        Route::middleware('role:employee')->group(function () {
            Route::get('/attendances/today', [AttendanceController::class, 'today']);
            Route::post('/attendances/check-in', [AttendanceController::class, 'checkIn']);
            Route::get('/attendances/summary', [AttendanceController::class, 'summary']);
            Route::get('/attendances/statistics', [AttendanceController::class, 'statistics']);
            Route::get('/attendances/history', [AttendanceController::class, 'history']);
            Route::post('/attendances/check-out', [AttendanceController::class, 'checkOut']);

            Route::post('/reimbursements', [ReimbursementController::class, 'store']);
            Route::get('/reimbursements', [ReimbursementController::class, 'index']);
        });

        Route::middleware('role:employee')->prefix('payroll')->group(function () {
            Route::get('/history', [PayrollController::class, 'history']);
            Route::get('/latest', [PayrollController::class, 'latest']);
            Route::get('/{id}', [PayrollController::class, 'show']);
            Route::get('/{id}/download', [PayrollController::class, 'downloadSlip']);
        });

        Route::post('/attendance/status', [AttendanceController::class, 'checkAttendanceStatus']);

        Route::middleware('role:employee')->prefix('overtime')->group(function () {
            Route::get('/', [OvertimeController::class, 'index']);
            Route::post('/', [OvertimeController::class, 'store']);
            Route::get('/{overtime}', [OvertimeController::class, 'show']);
            Route::delete('/{overtime}', [OvertimeController::class, 'destroy']);
        });

        Route::middleware('role:employee')->prefix('shifts')->group(function () {
            Route::get('/my-schedule', [EmployeeShiftController::class, 'mySchedule']);
            Route::get('/peers', [EmployeeShiftController::class, 'eligiblePeers']);
        });

        Route::middleware('role:employee')->prefix('shift-exchange')->group(function () {
            Route::get('/', [EmployeeShiftController::class, 'index']);
            Route::post('/', [EmployeeShiftController::class, 'store']);


        });


        Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
        Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
        Route::get('/leave-types', [LeaveTypeController::class, 'index']);
        Route::delete('/leave-requests/{id}', [LeaveRequestController::class, 'destroy']);
        Route::middleware('role:hr')->patch('/leave-requests/{id}/approve', [LeaveRequestController::class, 'approve']);


        Route::get('/contracts/reminder-h30', [EmployeeContractController::class, 'reminderH30']);
        Route::apiResource('contracts', EmployeeContractController::class);


        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('positions', PositionController::class);
        Route::apiResource('job-grades', JobGradeController::class);

        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class)->only(['index', 'show']);

        Route::apiResource('salary-components', SalaryComponentController::class);


        Route::post('/payroll/cutoff-attendance', [PayrollApiController::class, 'generateCutoff']);
        Route::post('/payroll/calculate', [PayrollApiController::class, 'calculateSalary']);
        Route::post('/payroll/{id}/approve-hr', [PayrollApiController::class, 'approveHr']);
        Route::post('/payroll/{id}/approve-finance', [PayrollApiController::class, 'approveFinance']);
        Route::post('/payroll/export-bank-csv', [PayrollApiController::class, 'exportBankCsv']);
        Route::get('/payroll/{id}/slip', [PayrollApiController::class, 'generateSlip']);
    });

});