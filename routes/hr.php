<?php

use Illuminate\Support\Facades\Route;

Route::prefix('hr')->name('hr.')->group(function () {

    Route::get('/dashboard', fn () => view('hr.dashboard'))->name('dashboard');

    Route::get('/karyawan', fn () => view('hr.karyawan.index'))->name('employees.index');
    Route::get('/karyawan/onboarding', fn () => view('hr.karyawan.onboarding'))->name('employees.onboarding');
    Route::get('/karyawan/{id}', fn ($id) => view('hr.karyawan.detail', ['id' => $id]))->name('employees.show');
    Route::get('/karyawan/{id}/edit', fn ($id) => view('hr.karyawan.edit', ['id' => $id]))->name('employees.edit');
    Route::get('/karyawan/{id}/dokumen', fn ($id) => view('hr.karyawan.dokumen', ['id' => $id]))->name('employees.documents');

    Route::get('/shift', fn () => view('hr.shift.index'))->name('shift.index');

    Route::get('/presensi', fn () => view('hr.presensi.index'))->name('attendance.index');

    Route::get('/persetujuan/cuti', fn () => view('hr.persetujuan.cuti'))->name('approvals.leave');
    Route::get('/persetujuan/lembur', fn () => view('hr.persetujuan.lembur'))->name('approvals.overtime');
    Route::get('/persetujuan/reimbursement', fn () => view('hr.persetujuan.reimbursement'))->name('approvals.reimbursement');

    Route::get('/penggajian', [\App\Http\Controllers\Web\HR\PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/penggajian/run', [\App\Http\Controllers\Web\HR\PayrollController::class, 'runPayroll'])->name('payroll.run');
    Route::get('/penggajian/{id}/slip', [\App\Http\Controllers\Web\HR\PayrollController::class, 'slip'])->name('payroll.slip');
    Route::get('/penggajian/komponen', [\App\Http\Controllers\Web\HR\SalaryComponentWebController::class, 'index'])->name('payroll.components');
    Route::post('/penggajian/komponen', [\App\Http\Controllers\Web\HR\SalaryComponentWebController::class, 'store'])->name('payroll.components.store');
    Route::put('/penggajian/komponen/{id}', [\App\Http\Controllers\Web\HR\SalaryComponentWebController::class, 'update'])->name('payroll.components.update');
    Route::delete('/penggajian/komponen/{id}', [\App\Http\Controllers\Web\HR\SalaryComponentWebController::class, 'destroy'])->name('payroll.components.destroy');
    Route::get('/kinerja', fn () => view('hr.kinerja.index'))->name('performance.index');
    Route::get('/struktur-organisasi', fn () => view('hr.struktur.index'))->name('structure.index');
    Route::get('/pengaturan', [\App\Http\Controllers\Web\HR\SettingController::class, 'index'])->name('settings.index');
    Route::put('/pengaturan/main', [\App\Http\Controllers\Web\HR\SettingController::class, 'updateMainSettings'])->name('settings.updateMain');
    Route::post('/pengaturan/leave-types', [\App\Http\Controllers\Web\HR\SettingController::class, 'storeLeaveType'])->name('settings.leave-types.store');
    Route::put('/pengaturan/leave-types/{id}', [\App\Http\Controllers\Web\HR\SettingController::class, 'updateLeaveType'])->name('settings.leave-types.update');
    Route::delete('/pengaturan/leave-types/{id}', [\App\Http\Controllers\Web\HR\SettingController::class, 'destroyLeaveType'])->name('settings.leave-types.destroy');
});