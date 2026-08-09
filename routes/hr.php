<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Web\HR\EmployeeController;
use App\Http\Controllers\Web\HR\PayrollController;
use App\Http\Controllers\Web\HR\SalaryComponentWebController;
use App\Http\Controllers\Web\HR\StructureController;
use App\Http\Controllers\Web\HR\SettingController;
use App\Http\Controllers\Web\HR\PresensiController;


Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {


    Route::get('/dashboard', fn() => view('hr.dashboard'))->name('dashboard');

    // KARYAWAN
    Route::get('/karyawan', [EmployeeController::class, 'indexWeb'])->name('employees.index');
    Route::get('/karyawan/{id}', [EmployeeController::class, 'showWeb'])->name('employees.show');
    Route::get('/karyawan/{id}/edit', [EmployeeController::class, 'editWeb'])->name('employees.edit');
    Route::get('/karyawan/onboarding', [EmployeeController::class, 'createWeb'])->name('employees.onboarding');
    Route::post('/karyawan', [EmployeeController::class, 'storeWeb'])->name('employees.storeWeb');
    Route::put('/karyawan/{id}', [EmployeeController::class, 'updateWeb'])->name('employees.updateWeb');
    Route::get('/karyawan/{id}/dokumen', fn($id) => view('hr.karyawan.dokumen', ['id' => $id]))->name('employees.documents');

    // SHIFT & PRESENSI
    Route::get('/shift', fn() => view('hr.shift.index'))->name('shift.index');
    Route::post('/shift/dummy-bulk-assign', fn() => redirect()->route('hr.shift.index')->with('success', 'Bulk Assignment Shift berhasil diproses!'))->name('shift.dummy-bulk-assign');
    Route::get('/presensi', [PresensiController::class, 'index'])->name('attendance.index');
    Route::get('/presensi/export', [PresensiController::class, 'export'])->name('attendance.export');

    // PERSETUJUAN (APPROVALS)
    Route::get('/persetujuan/cuti', fn() => view('hr.persetujuan.cuti'))->name('approvals.leave');
    Route::get('/persetujuan/lembur', fn() => view('hr.persetujuan.lembur'))->name('approvals.overtime');
    Route::get('/persetujuan/reimbursement', fn() => view('hr.persetujuan.reimbursement'))->name('approvals.reimbursement');
    Route::delete('/persetujuan/dummy-reject', fn() => redirect()->route('hr.dashboard')->with('success', 'Permintaan berhasil ditolak/dihapus!'))->name('approvals.dummy-reject');
    Route::post('/persetujuan/dummy-approve', fn() => redirect()->route('hr.dashboard')->with('success', 'Permintaan berhasil disetujui!'))->name('approvals.dummy-approve');

    // PENGGAJIAN (PAYROLL)
    Route::get('/penggajian', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/penggajian/run', [PayrollController::class, 'runPayroll'])->name('payroll.run');
    Route::get('/penggajian/{id}/slip', [PayrollController::class, 'slip'])->name('payroll.slip');

    // KOMPONEN GAJI
    Route::get('/penggajian/komponen', [SalaryComponentWebController::class, 'index'])->name('payroll.components');
    Route::post('/penggajian/komponen', [SalaryComponentWebController::class, 'store'])->name('payroll.components.store');
    Route::put('/penggajian/komponen/{id}', [SalaryComponentWebController::class, 'update'])->name('payroll.components.update');
    Route::delete('/penggajian/komponen/{id}', [SalaryComponentWebController::class, 'destroy'])->name('payroll.components.destroy');

    // KINERJA
    Route::get('/kinerja', fn() => view('hr.kinerja.index'))->name('performance.index');

    // STRUKTUR ORGANISASI
    Route::get('/struktur-organisasi', [StructureController::class, 'index'])->name('structure.index');
    Route::post('/struktur-organisasi/dept', [StructureController::class, 'storeDepartment'])->name('structure.dummy-dept');
    Route::post('/struktur-organisasi/grade', [StructureController::class, 'storeJobGrade'])->name('structure.dummy-grade');

    // PENGATURAN
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/pengaturan/main', [SettingController::class, 'updateMainSettings'])->name('settings.updateMain');
    Route::post('/pengaturan/leave-types', [SettingController::class, 'storeLeaveType'])->name('settings.leave-types.store');
    Route::put('/pengaturan/leave-types/{id}', [SettingController::class, 'updateLeaveType'])->name('settings.leave-types.update');
    Route::delete('/pengaturan/leave-types/{id}', [SettingController::class, 'destroyLeaveType'])->name('settings.leave-types.destroy');
    Route::get('/profil', fn() => view('hr.profile'))->name('profile');
});