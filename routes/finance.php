<?php

use Illuminate\Support\Facades\Route;

Route::prefix('finance')->name('finance.')->group(function () {

    Route::get('/dashboard', fn () => view('finance.dashboard'))->name('dashboard');

    Route::get('/reimbursement', fn () => view('finance.reimbursement.index'))->name('reimbursement.index');

    Route::get('/payroll', fn () => view('finance.payroll.index'))->name('payroll.index');

    Route::get('/export-bank', fn () => view('finance.export.index'))->name('export.index');

    Route::get('/disbursement', fn () => view('finance.disbursement.index'))->name('disbursement.index');

    Route::get('/pengaturan', fn () => view('finance.pengaturan.index'))->name('settings.index');
});