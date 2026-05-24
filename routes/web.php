<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\AuditController;

// ==========================================
// RUTE PUBLIK (Autentikasi)
// ==========================================
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'authenticate')->name('authenticate');
});

// ==========================================
// RUTE TERLINDUNG (Wajib Login)
// ==========================================
Route::middleware(['user.session'])->group(function () {
    
    // Logout diletakkan di dalam middleware (hanya yang sudah login yang bisa logout)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard & Profile
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', fn() => view('profile.profile'))->name('profile');

    // Audit Trails
    Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');

    // ------------------------------------------
    // MODUL INCIDENT LOGS
    // ------------------------------------------
    Route::prefix('incidents')->name('incidents.')->controller(IncidentController::class)->group(function () {
        // 1. Rute Statis (Harus di atas)
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/export', 'exportCsv')->name('export');

        // 2. Rute Dinamis dengan parameter {id} (Harus di bawah)
        Route::put('/{id}/severity', 'setSeverity')->name('set_severity');
        Route::put('/{id}/status', 'updateStatus')->name('update_status');
        Route::post('/{id}/resolve', 'resolveIncident')->name('resolve');
        Route::put('/{id}/verify', 'verifyIncident')->name('verify');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

});