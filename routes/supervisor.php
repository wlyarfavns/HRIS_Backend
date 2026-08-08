<?php

use Illuminate\Support\Facades\Route;

Route::prefix('supervisor')->name('supervisor.')->group(function () {

    Route::get('/dashboard', fn () => view('supervisor.dashboard'))->name('dashboard');

    Route::get('/persetujuan/cuti', fn () => view('supervisor.persetujuan.cuti'))->name('approvals.leave');
    Route::get('/persetujuan/lembur', fn () => view('supervisor.persetujuan.lembur'))->name('approvals.overtime');
    Route::get('/persetujuan/reimbursement', fn () => view('supervisor.persetujuan.reimbursement'))->name('approvals.reimbursement');

    Route::get('/laporan-kehadiran', fn () => view('supervisor.laporan.index'))->name('attendance.report');
    Route::get('/profil', fn () => view('supervisor.profile'))->name('profile');
});