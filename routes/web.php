<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/authenticate', [LoginController::class, 'authenticate'])->name('authenticate');

// TODO: ADD MIDDLEWARES FOR ADMIN AND WORKER ROUTES

Route::fallback(fn () => redirect('/'));
