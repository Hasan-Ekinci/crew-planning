<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AccountSettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;
use Illuminate\Auth\Middleware\Authenticate;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/redirect-to-correct-dashboard', [LoginController::class, 'redirectToCorrectDashboard'])->name('redirectToCorrectDashboard');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('/account-settings', [AccountSettingsController::class, 'index'])->name('accountSettings');
});

Route::middleware([AdminAuth::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/admin/roles', [AdminController::class, 'roles'])->name('adminRoles');

    route::get('/admin/get-roles', [AdminController::class, 'getRoles'])->name('adminGetRoles');
    route::get('/admin/get-employees-by-roles', [AdminController::class, 'getEmployeesByRole'])->name('adminGetEmplloyeesByRoles');

    route::get('/admin/get-shifts', [AdminController::class, 'getShifts'])->name('adminGetShifts');
    route::post('/admin/add-shift', [AdminController::class, 'addShift'])->name('adminAddShift');
    route::put('/admin/update-shift', [AdminController::class, 'updateShift'])->name('adminUpdateShift');
    Route::delete('/admin/delete-shift', [AdminController::class, 'deleteShift'])->name('adminDeleteShift');

    Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('adminEmployees');
});

Route::fallback(fn () => redirect('/'));
