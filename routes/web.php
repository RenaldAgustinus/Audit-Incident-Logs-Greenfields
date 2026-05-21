<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Rute Publik (Login)
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Terlindungi (Hanya yang sudah login)
Route::middleware(['user.session'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});