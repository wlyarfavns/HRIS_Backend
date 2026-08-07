<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterCompanyController;
use App\Http\Controllers\Web\UserManagementController;
use App\Http\Controllers\Web\HR\DepartmentController;
use App\Http\Controllers\Web\HR\PositionController;
use App\Http\Controllers\Web\HR\EmployeeController;

Route::get('/register', [RegisterCompanyController::class, 'create'])->name('register');
Route::post('/register', [RegisterCompanyController::class, 'store'])->name('register.store');
Route::get('/register/success', [RegisterCompanyController::class, 'success'])->name('register.success');

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', function () {
        return "Ini halaman Dashboard Web";
    })->name('dashboard');

Route::middleware('role:company')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);
    });

Route::middleware('role:hr')->group(function () {
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('positions', PositionController::class);
        Route::apiResource('employees', EmployeeController::class);
    });

});

require __DIR__.'/admin.php';
require __DIR__.'/hr.php';
require __DIR__.'/finance.php';
