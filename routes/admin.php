<?php

use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', fn () => view('admin.dashboard'))->name('dashboard');

    // Profil Perusahaan — single-tenant (1 perusahaan, bukan daftar cabang)
    Route::get('/perusahaan', fn () => view('admin.perusahaan.profil'))->name('company.index');
    Route::get('/perusahaan/edit', fn () => view('admin.perusahaan.profil', ['editing' => true]))->name('company.edit');

    Route::get('/pengguna', fn () => view('admin.pengguna.index'))->name('users.index');
    Route::get('/pengguna/tambah', fn () => view('admin.pengguna.create'))->name('users.create');
    Route::get('/pengguna/{id}/edit', fn ($id) => view('admin.pengguna.edit', ['id' => $id]))->name('users.edit');

    Route::get('/log-aktivitas', fn () => view('admin.log-aktivitas.index'))->name('logs.index');
});
