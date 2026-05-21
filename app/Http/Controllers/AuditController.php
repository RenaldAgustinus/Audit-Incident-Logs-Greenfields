<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function index()
    {
        // Menampilkan riwayat log (Immutable / Read-only)
        $audits = DB::table('audit_trails')
            ->join('users', 'audit_trails.user_id', '=', 'users.id')
            ->join('incident_logs', 'audit_trails.incident_id', '=', 'incident_logs.id')
            ->select('audit_trails.*', 'users.full_name', 'users.role', 'incident_logs.incident_title')
            ->orderBy('audit_trails.created_at', 'desc')
            ->paginate(20);

        return view('audit.audit', compact('audits'));
    }
}