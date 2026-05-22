<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AuditController;

// Rute Publik (Login)
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Terlindungi (Hanya yang sudah login)
Route::middleware(['user.session'])->group(function () {
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
// Route untuk Incident Logs
Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');
    
// Route untuk Audit Trails
Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');

// Route untuk Update Status
    Route::put('/incidents/{id}/status', [IncidentController::class, 'updateStatus'])->name('incidents.update_status');
    
// Route untuk Soft Delete
Route::delete('/incidents/{id}', [IncidentController::class, 'destroy'])->name('incidents.destroy');
    
    Route::get('/profile', function () {
        return view('profile.profile'); 
    })->name('profile');
    
});