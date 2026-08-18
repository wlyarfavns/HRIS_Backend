<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Finance\ReimbursementController;
use App\Http\Controllers\Web\Finance\PayrollController;
use App\Http\Controllers\Web\Finance\ExportController;
use App\Http\Controllers\Web\Finance\DisbursementController;
use App\Http\Controllers\Web\Finance\DashboardController;
use App\Http\Controllers\Web\Shared\ProfileController;

Route::middleware(['auth', 'role:finance'])->prefix('finance')->name('finance.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/reimbursement', [ReimbursementController::class, 'index'])->name('reimbursement.index');
    Route::patch('/reimbursement/{reimbursement}/action', [ReimbursementController::class, 'action'])->name('reimbursement.action');

    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::get('/payroll/{batch}', [PayrollController::class, 'show'])->name('payroll.show');
    Route::post('/payroll/{batch}/approve', [PayrollController::class, 'approve'])->name('payroll.approve');

    Route::get('/export-bank', [ExportController::class, 'index'])->name('export.index');
    Route::post('/export-bank/{batch}/generate', [ExportController::class, 'generate'])->name('export.generate');
    Route::get('/export-bank/{batch}/{bankCode}/download', [ExportController::class, 'download'])->name('export.download');

    Route::get('/disbursement', [DisbursementController::class, 'index'])->name('disbursement.index');
    Route::post('/disbursement/{batch}/mark-disbursed', [DisbursementController::class, 'markDisbursed'])->name('disbursement.markDisbursed');
    Route::post('/disbursement/{batch}/mark-published', [DisbursementController::class, 'markPublished'])->name('disbursement.markPublished');
    Route::get('/disbursement/{id}/slip', [PayrollController::class, 'slip'])->name('disbursement.slip');

    Route::get('/pengaturan', fn() => view('finance.pengaturan.index'))->name('settings.index');

    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});