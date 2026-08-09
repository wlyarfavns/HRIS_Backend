<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\company\UserManagementController;
use App\Http\Controllers\Web\company\CompanyProfileController;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

    Route::middleware('role:company|hr')->group(function () {
        Route::get('/pengguna', [UserManagementController::class, 'indexWeb'])->name('users.index');
        Route::get('/pengguna/tambah', [UserManagementController::class, 'createWeb'])->name('users.create');
        Route::post('/pengguna', [UserManagementController::class, 'storeWeb'])->name('users.store');
        Route::get('/pengguna/{id}/edit', [UserManagementController::class, 'editWeb'])->name('users.edit');
        Route::put('/pengguna/{id}', [UserManagementController::class, 'updateWeb'])->name('users.updateWeb');
        Route::delete('/pengguna/{id}', [UserManagementController::class, 'destroyWeb'])->name('users.destroyWeb');
    });
    Route::get('/perusahaan', [CompanyProfileController::class, 'indexWeb'])->name('company.index');
    Route::get('/perusahaan/tambah', fn() => view('admin.perusahaan.create'))->name('company.create');
    Route::get('/perusahaan/{id}/edit', [CompanyProfileController::class, 'editWeb'])->name('company.edit');
    Route::put('/perusahaan/update', [CompanyProfileController::class, 'updateWeb'])->name('company.update');

    Route::get('/struktur-organisasi', fn() => view('admin.perusahaan.struktur-organisasi'))->name('org-structure.index');



    Route::get('/keamanan', fn() => view('admin.keamanan.index'))->name('security.index');

    Route::get('/modul/hr', fn() => view('admin.modul.hr'))->name('modules.hr');
    Route::get('/modul/finance', fn() => view('admin.modul.finance'))->name('modules.finance');

    Route::get('/langganan', fn() => view('admin.langganan.index'))->name('billing.index');
    Route::get('/log-aktivitas', fn() => view('admin.log-aktivitas.index'))->name('logs.index');
    Route::get('/profil', fn() => view('admin.profile'))->name('profile');
});