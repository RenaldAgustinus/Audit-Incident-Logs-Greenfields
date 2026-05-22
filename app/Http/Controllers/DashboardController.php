<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // PERBAIKAN 1: Tambahkan Request $request di sini agar bisa menangkap inputan Search Bar
    public function index(Request $request)
    {
        // 1. Ambil data URGENT (Critical & Open/Investigating) - Maksimal 5 agar UI tidak rusak
        $criticalIncidents = DB::table('incident_logs')
            ->select('id', 'incident_title', 'description', 'status', 'created_at')
            ->where('severity_level', 'critical')
            ->whereIn('status', ['open', 'investigating'])
            ->where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 2. Siapkan Query Dasar untuk RECENT LOGS
        $recentQuery = DB::table('incident_logs')
            ->join('users', 'incident_logs.reported_by', '=', 'users.id')
            ->select('incident_logs.*', 'users.full_name as reporter_name')
            ->where('incident_logs.severity_level', '!=', 'critical')
            ->where('incident_logs.is_deleted', false);

        // PERBAIKAN 2: Logika Pencarian ditambahkan di sini
        if ($request->filled('search')) {
            $recentQuery->where(function($q) use ($request) {
                $q->where('incident_logs.incident_title', 'like', '%' . $request->search . '%')
                  ->orWhere('users.full_name', 'like', '%' . $request->search . '%');
            });
        }

        // PERBAIKAN 3: Eksekusi data (tambahkan withQueryString agar search tidak hilang pas pindah halaman)
        $recentLogs = $recentQuery->orderBy('incident_logs.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // 3. Hitung Widget Statistik
        $totalIncidents = DB::table('incident_logs')->where('is_deleted', false)->count();
        
        $criticalOpenCount = DB::table('incident_logs')
            ->where('severity_level', 'critical')
            ->whereIn('status', ['open', 'investigating']) 
            ->where('is_deleted', false)
            ->count();
            
        $pendingAuditCount = DB::table('incident_logs')
            ->where('status', 'open')
            ->where('is_deleted', false)
            ->count();

        return view('dashboard.dashboard', compact('criticalIncidents', 'recentLogs', 'totalIncidents', 'criticalOpenCount', 'pendingAuditCount'));
    }
}