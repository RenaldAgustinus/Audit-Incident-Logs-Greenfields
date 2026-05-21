<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
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

        // 2. Ambil data RECENT LOGS (Low/Medium atau yang sudah Resolved) - Pagination
        $recentLogs = DB::table('incident_logs')
            ->join('users', 'incident_logs.reported_by', '=', 'users.id')
            ->select('incident_logs.*', 'users.full_name as reporter_name')
            ->where('incident_logs.severity_level', '!=', 'critical')
            ->where('incident_logs.is_deleted', false)
            ->orderBy('incident_logs.created_at', 'desc')
            ->paginate(10); // Otomatis aman dan ringan

        // 3. Hitung Widget Statistik
        $totalIncidents = DB::table('incident_logs')->where('is_deleted', false)->count();
        $criticalOpenCount = DB::table('incident_logs')
            ->where('severity_level', 'critical')
            ->where('status', 'open')
            ->where('is_deleted', false)
            ->count();

        return view('dashboard', compact('criticalIncidents', 'recentLogs', 'totalIncidents', 'criticalOpenCount'));
    }
}
