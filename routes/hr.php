<?php

use Illuminate\Support\Facades\Route;

Route::prefix('hr')->name('hr.')->group(function () {

    Route::get('/dashboard', fn () => view('hr.dashboard'))->name('dashboard');

    Route::get('/karyawan', fn () => view('hr.karyawan.index'))->name('employees.index');
    Route::get('/karyawan/onboarding', fn () => view('hr.karyawan.onboarding'))->name('employees.onboarding');
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
    Route::get('/kinerja', fn () => view('hr.kinerja.index'))->name('performance.index');
    Route::get('/struktur-organisasi', fn () => view('hr.struktur.index'))->name('structure.index');
    Route::get('/pengaturan', fn () => view('hr.pengaturan.index'))->name('settings.index');
});