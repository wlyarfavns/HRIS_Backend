<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Web\HR\EmployeeController;
use App\Http\Controllers\Web\HR\PayrollController;
use App\Http\Controllers\Web\HR\LeaveController;
use App\Http\Controllers\Web\HR\OvertimeController;
use App\Http\Controllers\Web\HR\SalaryComponentWebController;
use App\Http\Controllers\Web\HR\StructureController;
use App\Http\Controllers\Web\HR\ReimbursementController;
use App\Http\Controllers\Web\HR\SettingController;
use App\Http\Controllers\Web\HR\PresensiController;
use App\Http\Controllers\Web\HR\ShiftController;
use App\Http\Controllers\Web\HR\DashboardController;
use App\Http\Controllers\Web\Shared\ProfileController;


Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // ── KARYAWAN ─────────────────────────────────────────────────────────────
    Route::get('/karyawan', [EmployeeController::class, 'indexWeb'])->name('employees.index');
    Route::get('/karyawan/onboarding', [EmployeeController::class, 'createWeb'])->name('employees.onboarding');
    Route::post('/karyawan', [EmployeeController::class, 'storeWeb'])->name('employees.storeWeb');

    Route::get('/karyawan/{id}', [EmployeeController::class, 'showWeb'])->name('employees.show');
    Route::get('/karyawan/{id}/edit', [EmployeeController::class, 'editWeb'])->name('employees.edit');
    Route::put('/karyawan/{id}', [EmployeeController::class, 'updateWeb'])->name('employees.updateWeb');
    Route::get('/karyawan/{id}/dokumen', fn($id) => view('hr.karyawan.dokumen', ['id' => $id]))->name('employees.documents');

    // ── SHIFT & ROSTER ────────────────────────────────────────────────────────
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
    Route::post('/shift/bulk-assign', [ShiftController::class, 'bulkAssign'])->name('shift.bulk-assign');
    Route::post('/shift/cell', [ShiftController::class, 'updateCell'])->name('shift.update-cell');
    Route::post('/shift/swap/{swap}/approve', [ShiftController::class, 'approveSwap'])->name('shift.swap.approve');
    Route::post('/shift/swap/{swap}/reject', [ShiftController::class, 'rejectSwap'])->name('shift.swap.reject');
    Route::post('/shift/geofencing', [ShiftController::class, 'updateGeofencing'])->name('shift.geofencing.update');

    // ── PRESENSI ──────────────────────────────────────────────────────────────
    Route::get('/presensi', [PresensiController::class, 'index'])->name('attendance.index');
    Route::get('/presensi/export', [PresensiController::class, 'export'])->name('attendance.export');

    // ── PERSETUJUAN CUTI ──────────────────────────────────────────────────────
    Route::get('/persetujuan/cuti', [LeaveController::class, 'index'])->name('approvals.leave');
    Route::post('/persetujuan/cuti/{id}/approve', [LeaveController::class, 'approve'])->name('approvals.leave.approve');
    Route::post('/persetujuan/cuti/{id}/reject', [LeaveController::class, 'reject'])->name('approvals.leave.reject');

    Route::get('/persetujuan/lembur', [OvertimeController::class, 'index'])
        ->name('approvals.overtime');
    Route::post('/persetujuan/lembur/{overtime}/lock', [OvertimeController::class, 'lock'])
        ->name('approvals.overtime.lock');
    Route::post('/persetujuan/lembur/{overtime}/reject', [OvertimeController::class, 'reject'])
        ->name('approvals.overtime.reject');
    Route::get('/persetujuan/reimbursement', [ReimbursementController::class, 'index'])->name('approvals.reimbursement');
    Route::patch('/persetujuan/reimbursement/{reimbursement}/action', [ReimbursementController::class, 'verify'])->name('approvals.reimbursement.action');

    // ── PENGGAJIAN ────────────────────────────────────────────────────────────
    Route::get('/penggajian', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/penggajian/run', [PayrollController::class, 'showRunPage'])->name('payroll.showRun');
    Route::post('/penggajian/run', [PayrollController::class, 'runPayroll'])->name('payroll.run');

    // BARU: approval pipeline dari web (sebelumnya cuma ada di API)
    Route::post('/penggajian/approve-hr', [PayrollController::class, 'approveHr'])->name('payroll.approveHr');
    Route::post('/penggajian/approve-finance', [PayrollController::class, 'approveFinance'])->name('payroll.approveFinance');

    // BARU: export rekap XLSX (beda dari exportBankCsv di API yang formatnya untuk upload bank)
    Route::get('/penggajian/export', [PayrollController::class, 'exportXlsx'])->name('payroll.export');

    Route::get('/penggajian/komponen', [SalaryComponentWebController::class, 'index'])->name('payroll.components');
    Route::post('/penggajian/komponen', [SalaryComponentWebController::class, 'store'])->name('payroll.components.store');
    Route::put('/penggajian/komponen/{id}', [SalaryComponentWebController::class, 'update'])->name('payroll.components.update');
    Route::delete('/penggajian/komponen/{id}', [SalaryComponentWebController::class, 'destroy'])->name('payroll.components.destroy');

    Route::get('/penggajian/{id}/slip', [PayrollController::class, 'slip'])->name('payroll.slip');

    // ── KINERJA ───────────────────────────────────────────────────────────────
    Route::get('/kinerja', fn() => view('hr.kinerja.index'))->name('performance.index');

    // ── STRUKTUR ORGANISASI ───────────────────────────────────────────────────
    Route::get('/struktur-organisasi', [StructureController::class, 'index'])->name('structure.index');
    Route::post('/struktur-organisasi/dept', [StructureController::class, 'storeDepartment'])->name('structure.dummy-dept');
    Route::post('/struktur-organisasi/grade', [StructureController::class, 'storeJobGrade'])->name('structure.dummy-grade');

    // ── PENGATURAN ────────────────────────────────────────────────────────────
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/pengaturan/main', [SettingController::class, 'updateMainSettings'])->name('settings.updateMain');
    Route::post('/pengaturan/leave-types', [SettingController::class, 'storeLeaveType'])->name('settings.leave-types.store');
    Route::put('/pengaturan/leave-types/{id}', [SettingController::class, 'updateLeaveType'])->name('settings.leave-types.update');
    Route::delete('/pengaturan/leave-types/{id}', [SettingController::class, 'destroyLeaveType'])->name('settings.leave-types.destroy');

    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profil/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});