<?php

require __DIR__.'/admin.php';
require __DIR__.'/hr.php';
require __DIR__.'/finance.php';

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterCompanyController;
use App\Http\Controllers\Web\UserManagementController;
use App\Http\Controllers\Web\HR\DepartmentController;
use App\Http\Controllers\Web\HR\PositionController;
use App\Http\Controllers\Mobile\Employee\EmployeeController;
use App\Http\Controllers\Web\company\DashboardController; 
use App\Http\Controllers\Web\HR\CompanySettingsController;

/*
|--------------------------------------------------------------------------
| Public Routes (WEB)
|--------------------------------------------------------------------------
*/
Route::redirect('/', '/login');
Route::get('/register', [RegisterCompanyController::class, 'create'])->name('register');
Route::post('/register', [RegisterCompanyController::class, 'store'])->name('register.store');
Route::get('/register/success', [RegisterCompanyController::class, 'success'])->name('register.success');
Route::get('/login', [LoginController::class, 'create'])->name('login'); 
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

/*
|--------------------------------------------------------------------------
| Protected Routes (WEB)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Hak Akses: Company (Manajemen Akun HR/Manajerial)
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
    | Hak Akses: HR (Master Data SDM)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:hr')->group(function () {
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('positions', PositionController::class);
        Route::apiResource('employees', EmployeeController::class);
        Route::put('/hr/company-settings/attendance', [CompanySettingsController::class, 'updateAttendanceRules']);
    });

});

require __DIR__.'/admin.php';
require __DIR__.'/hr.php';
require __DIR__.'/finance.php';
require __DIR__.'/supervisor.php';

