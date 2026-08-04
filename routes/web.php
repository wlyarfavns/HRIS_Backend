<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterCompanyController;
use App\Http\Controllers\Web\UserManagementController;
use App\Http\Controllers\Web\Company\DepartmentController;

/*
|--------------------------------------------------------------------------
| Public Route
|--------------------------------------------------------------------------
|
*/
Route::post('/register', [RegisterCompanyController::class, 'store'])
    ->name('register.store');

Route::post('/login', [LoginController::class, 'store'])
    ->name('login.store');


/*
|--------------------------------------------------------------------------
| Protected Route
|--------------------------------------------------------------------------
| 
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

    Route::get('/dashboard', function () {
        return "Ini halaman Dashboard Web";
    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Hanya Company
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:company')->group(function () {

        Route::prefix('users')->group(function () {

            Route::get('/', [UserManagementController::class, 'index']);

            Route::post('/', [UserManagementController::class, 'store']);

            Route::put('/{user}', [UserManagementController::class, 'update']);

            Route::delete('/{user}', [UserManagementController::class, 'destroy']);

        });

    });


    Route::middleware(['auth', 'role:company'])->group(function () {

        Route::post('/users', [UserManagementController::class, 'store']);
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::put('/users/{user}', [UserManagementController::class, 'update']);
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy']);

        Route::apiResource('departments', DepartmentController::class);

    });
});