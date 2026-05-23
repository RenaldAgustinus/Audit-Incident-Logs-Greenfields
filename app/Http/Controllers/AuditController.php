<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditController extends Controller
{
    public function index()
    {
        // 1. Ambil daftar insiden (Induknya)
        $incidents = DB::table('incident_logs')
            ->join('users', 'incident_logs.reported_by', '=', 'users.id')
            ->select('incident_logs.id', 'incident_logs.incident_title', 'incident_logs.status', 'incident_logs.created_at', 'users.full_name as reporter_name')
            ->orderBy('incident_logs.created_at', 'desc')
            ->paginate(10);

        // 2. Ambil detail log audit HANYA untuk insiden yang tampil di halaman ini (Biar hemat RAM)
        $incidentIds = collect($incidents->items())->pluck('id');
        
        $trails = DB::table('audit_trails')
            ->join('users', 'audit_trails.user_id', '=', 'users.id')
            ->whereIn('incident_id', $incidentIds)
            ->select('audit_trails.*', 'users.full_name as actor_name', 'users.role as actor_role')
            ->orderBy('audit_trails.created_at', 'desc') // Urutan terbaru di atas
            ->get()
            ->groupBy('incident_id');

        return view('audit.audit', compact('incidents', 'trails'));
    }
}