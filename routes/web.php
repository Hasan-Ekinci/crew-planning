<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/redirect-to-correct-dashboard', [LoginController::class, 'redirectToCorrectDashboard'])->name('redirectToCorrectDashboard');

// TODO: ADD MIDDLEWARES FOR ADMIN AND WORKER ROUTES

Route::middleware([AdminAuth::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/admin/employees', [AdminController::class, 'employees'])->name('adminEmployees');
    Route::get('/admin/roles', [AdminController::class, 'roles'])->name('adminRoles');
});

Route::fallback(fn () => redirect('/'));
