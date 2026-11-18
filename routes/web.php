<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CrewController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AccountSettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\CrewAuth;
use Illuminate\Auth\Middleware\Authenticate;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');
Route::get('/redirect-to-correct-dashboard', [LoginController::class, 'redirectToCorrectDashboard'])->name('redirectToCorrectDashboard');

Route::middleware([Authenticate::class])->group(function () {
    Route::get('/account-settings', [AccountSettingsController::class, 'index'])->name('accountSettings');
    Route::post('/account-settings/save', [AccountSettingsController::class, 'save'])->name('saveAccountSettings');

    Route::middleware([CrewAuth::class])->group(function () {
        Route::get('/crew', [CrewController::class, 'index'])->name('crew');
        Route::get('/crew/get-shifts', [CrewController::class, 'getShifts'])->name('crewGetShifts');
        Route::get('/crew/find-shifts', [CrewController::class, 'findShifts'])->name('crewFindShifts');
        Route::get('/crew/get-available-shifts', [CrewController::class, 'getAvailableShifts'])->name('crewGetAvailableShifts');
        Route::post('/crew/accept-shift', [CrewController::class, 'acceptShift'])->name('crewAcceptShift');
    });

    Route::middleware([AdminAuth::class])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin');
        Route::get('/admin/roles', [AdminController::class, 'roles'])->name('adminRoles');
        Route::post('/admin/roles/save', [AdminController::class, 'saveRole'])->name('adminSaveRole');
        Route::get('/admin/roles/{id}/users', [AdminController::class, 'getRoleUsers'])->whereNumber('id')->name('adminGetRoleUsers');

        route::get('/admin/get-roles', [AdminController::class, 'getRoles'])->name('adminGetRoles');
        route::get('/admin/get-employees-by-roles', [AdminController::class, 'getEmployeesByRole'])->name('adminGetEmplloyeesByRoles');

        route::get('/admin/get-shifts', [AdminController::class, 'getShifts'])->name('adminGetShifts');
        route::post('/admin/add-shift', [AdminController::class, 'addShift'])->name('adminAddShift');
        route::put('/admin/update-shift', [AdminController::class, 'updateShift'])->name('adminUpdateShift');
        Route::delete('/admin/delete-shift', [AdminController::class, 'deleteShift'])->name('adminDeleteShift');

        Route::get('/admin/employees', [EmployeeController::class, 'index'])->name('adminEmployees');
        Route::get('/admin/employees/create', [EmployeeController::class, 'create'])->name('adminCreateEmployee');
        Route::get('/admin/employees/edit/{id}', [EmployeeController::class, 'edit'])->whereNumber('id')->name('adminEditEmployee');
        Route::post('/admin/employees/save', [EmployeeController::class, 'save'])->name('adminSaveEmployee');
    });
});

Route::fallback(fn () => redirect('/'));
