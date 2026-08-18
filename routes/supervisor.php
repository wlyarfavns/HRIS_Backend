<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Supervisor\AttendanceReportController;
use App\Http\Controllers\Web\Supervisor\LeaveApprovalController;
use App\Http\Controllers\Web\Supervisor\OvertimeApprovalController;
use App\Http\Controllers\Web\Supervisor\ReimbursementApprovalController;
use App\Http\Controllers\Web\Supervisor\SupervisorDashboardController;
use App\Http\Controllers\Web\Supervisor\ShiftApprovalController;
use App\Http\Controllers\Web\Supervisor\TeamController;
use App\Http\Controllers\Web\Shared\ProfileController;

Route::prefix('supervisor')->name('supervisor.')->middleware(['auth', 'role:supervisor'])->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/persetujuan/cuti', [LeaveApprovalController::class, 'index'])->name('approvals.leave');
    Route::post('/persetujuan/cuti/{id}/approve', [LeaveApprovalController::class, 'approve'])->name('approvals.leave.approve');
    Route::post('/persetujuan/cuti/{id}/reject', [LeaveApprovalController::class, 'reject'])->name('approvals.leave.reject');
    Route::get('/persetujuan/lembur', [OvertimeApprovalController::class, 'index'])->name('approvals.overtime');
    Route::post('/persetujuan/lembur/{overtime}/approve', [OvertimeApprovalController::class, 'approve'])->name('approvals.overtime.approve');
    Route::post('/persetujuan/lembur/{overtime}/reject', [OvertimeApprovalController::class, 'reject'])->name('approvals.overtime.reject');
    Route::get('/persetujuan/reimbursement', [ReimbursementApprovalController::class, 'index'])->name('approvals.reimbursement');
    Route::patch('/persetujuan/reimbursement/{reimbursement}/action', [ReimbursementApprovalController::class, 'action'])->name('approvals.reimbursement.action');

    Route::get('/persetujuan/shift', [ShiftApprovalController::class, 'index'])->name('approvals.shift');
    Route::post('/persetujuan/shift/{swap}/approve', [ShiftApprovalController::class, 'approve'])->name('approvals.shift.approve');
    Route::post('/persetujuan/shift/{swap}/reject', [ShiftApprovalController::class, 'reject'])->name('approvals.shift.reject');


    Route::get('/laporan-kehadiran', [AttendanceReportController::class, 'index'])->name('attendance.report');

    Route::get('/tim', [TeamController::class, 'index'])->name('team.index');

    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});