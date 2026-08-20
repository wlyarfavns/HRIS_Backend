<?php

require __DIR__ . '/admin.php';
require __DIR__ . '/hr.php';
require __DIR__ . '/finance.php';
require __DIR__ . '/supervisor.php';

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\LoginController;
use App\Http\Controllers\Web\Auth\RegisterCompanyController;
use App\Http\Controllers\Web\company\UserManagementController;
use App\Http\Controllers\Web\company\DepartmentController;
use App\Http\Controllers\Web\HR\PositionController;
use App\Http\Controllers\Api\Employee\EmployeeController;
use App\Http\Controllers\Web\company\DashboardController;
use App\Http\Controllers\Web\Shared\NotificationController;
use App\Http\Controllers\Web\Auth\PasswordResetController;

Route::redirect('/', '/login');
Route::get('/register', [RegisterCompanyController::class, 'create'])->name('register');
Route::post('/register', [RegisterCompanyController::class, 'store'])->name('register.store');
Route::get('/register/success', [RegisterCompanyController::class, 'success'])->name('register.success');
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');

Route::get('/lupa-kata-sandi', [PasswordResetController::class, 'requestForm'])->name('password.request');
Route::post('/lupa-kata-sandi', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/lupa-kata-sandi/otp', [PasswordResetController::class, 'verifyOtpForm'])->name('password.verify.form');
Route::post('/lupa-kata-sandi/otp', [PasswordResetController::class, 'processOtp'])->name('password.verify');
Route::get('/atur-ulang-sandi/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/atur-ulang-sandi', [PasswordResetController::class, 'updatePassword'])->name('password.update');

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');

    Route::get('/notifikasi/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifikasi/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');


    Route::middleware('role:hr')->group(function () {
        Route::apiResource('departments', DepartmentController::class);
        Route::apiResource('positions', PositionController::class);
        Route::apiResource('employees', EmployeeController::class);
    });

});

require __DIR__ . '/admin.php';
require __DIR__ . '/hr.php';
require __DIR__ . '/finance.php';
require __DIR__ . '/supervisor.php';

