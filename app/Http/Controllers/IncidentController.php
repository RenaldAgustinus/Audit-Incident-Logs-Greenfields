<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncidentController extends Controller
{
    public function index()
    {
        // Menampilkan semua data untuk tabel logs
        $logs = DB::table('incident_logs')
            ->join('users', 'incident_logs.reported_by', '=', 'users.id')
            ->select('incident_logs.*', 'users.full_name as reporter_name')
            ->where('incident_logs.is_deleted', false)
            ->orderBy('incident_logs.created_at', 'desc')
            ->paginate(15);

        return view('incident.incident', compact('logs'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'incident_title' => 'required|max:150',
            'description' => 'required',
            'severity_level' => 'required|in:low,medium,critical'
        ]);

        $now = Carbon::now();

        // 1. Insert ke tabel incident_logs dan ambil ID barunya
        $incidentId = DB::table('incident_logs')->insertGetId([
            'reported_by' => session('user_id'),
            'incident_title' => $request->incident_title,
            'description' => $request->description,
            'severity_level' => $request->severity_level,
            'status' => 'open',
            'is_deleted' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Insert OTOMATIS ke tabel audit_trails (Ini requirement krusial!)
        DB::table('audit_trails')->insert([
            'incident_id' => $incidentId,
            'user_id' => session('user_id'),
            'action' => 'CREATED',
            'old_value' => null,
            'new_value' => 'Insiden baru dilaporkan: ' . $request->severity_level,
            'created_at' => $now,
        ]);

        return back()->with('success', 'Insiden berhasil dilaporkan!');
    }
}