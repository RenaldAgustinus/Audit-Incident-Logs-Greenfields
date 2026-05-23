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
// Route untuk Flow 1 (Operator submit form)
Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store');

// Route untuk Flow 2 (Supervisor atur severity)
Route::put('/incidents/{id}/severity', [IncidentController::class, 'setSeverity'])->name('incidents.set_severity');
    
// Route untuk Audit Trails
Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');

// Route untuk Update Status
    Route::put('/incidents/{id}/status', [IncidentController::class, 'updateStatus'])->name('incidents.update_status');
    
// Route untuk Soft Delete
Route::delete('/incidents/{id}', [IncidentController::class, 'destroy'])->name('incidents.destroy');

// Route untuk Export CSV
Route::get('/incidents/export', [IncidentController::class, 'exportCsv'])->name('incidents.export');
    
    Route::get('/profile', function () {
        return view('profile.profile'); 
    })->name('profile');
    
// Route untuk Flow 4 (Operator Upload Tindak Lanjut)
Route::post('/incidents/{id}/resolve', [IncidentController::class, 'resolveIncident'])->name('incidents.resolve');

// Route untuk Flow 5 (Supervisor Approve/Reject)
Route::put('/incidents/{id}/verify', [IncidentController::class, 'verifyIncident'])->name('incidents.verify');
});