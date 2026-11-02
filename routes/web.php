<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/redirect-to-correct-dashboard', [LoginController::class, 'redirectToCorrectDashboard'])->name('redirectToCorrectDashboard');

Route::middleware([AdminAuth::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');
    Route::get('/admin/employees', [AdminController::class, 'employees'])->name('adminEmployees');
    Route::get('/admin/roles', [AdminController::class, 'roles'])->name('adminRoles');

    route::get('/admin/get-roles', [AdminController::class, 'getRoles'])->name('adminGetRoles');
    route::get('/admin/get-employees-by-roles', [AdminController::class, 'getEmployeesByRole'])->name('adminGetEmplloyeesByRoles');
    
    route::get('/admin/get-shifts', [AdminController::class, 'getShifts'])->name('adminGetShifts');
    route::post('/admin/add-shift', [AdminController::class, 'addShift'])->name('adminAddShift');
    route::put('/admin/update-shift', [AdminController::class, 'updateShift'])->name('adminUpdateShift');
});

Route::fallback(fn () => redirect('/'));
