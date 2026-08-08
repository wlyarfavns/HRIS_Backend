<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

    Route::get('/perusahaan', fn () => view('admin.perusahaan.manajemen-perusahaan'))->name('companies.index');
    Route::get('/perusahaan/tambah', fn () => view('admin.perusahaan.create'))->name('companies.create');
    Route::post('/perusahaan', fn () => redirect()->route('admin.companies.index')->with('success', 'Profil perusahaan berhasil disimpan!'))->name('companies.update');
    Route::get('/perusahaan/{id}/edit', fn ($id) => view('admin.perusahaan.edit', ['id' => $id]))->name('companies.edit');
    
    Route::get('/struktur-organisasi', fn () => view('admin.perusahaan.struktur-organisasi'))->name('org-structure.index');

    Route::get('/pengguna', fn () => view('admin.pengguna.index'))->name('users.index');
    Route::post('/pengguna', [\App\Http\Controllers\Web\UserManagementController::class, 'storeWeb'])->name('users.store');
    Route::get('/pengguna/tambah', fn () => view('admin.pengguna.create'))->name('users.create');
    Route::get('/pengguna/{id}/edit', fn ($id) => view('admin.pengguna.edit', ['id' => $id]))->name('users.edit');
    Route::put('/pengguna/{id}', [\App\Http\Controllers\Web\UserManagementController::class, 'updateWeb'])->name('users.updateWeb');

    Route::get('/keamanan', fn () => view('admin.keamanan.index'))->name('security.index');

    Route::get('/modul/hr', fn () => view('admin.modul.hr'))->name('modules.hr');
    Route::get('/modul/finance', fn () => view('admin.modul.finance'))->name('modules.finance');

    Route::get('/langganan', fn () => view('admin.langganan.index'))->name('billing.index');
    Route::get('/log-aktivitas', fn () => view('admin.log-aktivitas.index'))->name('logs.index');
    Route::get('/profil', fn () => view('admin.profile'))->name('profile');
});
