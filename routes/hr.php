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

    Route::get('/penggajian', fn () => view('hr.penggajian.index'))->name('payroll.index');
    Route::get('/penggajian/komponen-gaji', fn () => view('hr.penggajian.komponen'))->name('payroll.components');
    Route::get('/penggajian/{id}/slip', fn ($id) => view('hr.penggajian.slip', ['id' => $id]))->name('payroll.slip');

    Route::get('/struktur-organisasi', fn () => view('hr.struktur.index'))->name('structure.index');
    Route::get('/pengaturan', fn () => view('hr.pengaturan.index'))->name('settings.index');
    Route::get('/profil', fn () => view('hr.profile'))->name('profile');
});
