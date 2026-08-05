<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterCompanyController;
use App\Http\Controllers\Web\UserManagementController;
use App\Http\Controllers\Web\HR\DepartmentController;
use App\Http\Controllers\Web\HR\PositionController;
use App\Http\Controllers\Web\HR\EmployeeController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [RegisterCompanyController::class, 'store'])->name('register.store');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', function () {
        return "Ini halaman Dashboard Web";
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Hak Akses: Company (Fokus pada manajemen akun jajaran HR/Manajerial)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:company')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | Hak Akses: HR (Fokus pada Master Data SDM)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:hr')->group(function () {
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('positions', PositionController::class);
        Route::apiResource('employees', EmployeeController::class);
    });

});