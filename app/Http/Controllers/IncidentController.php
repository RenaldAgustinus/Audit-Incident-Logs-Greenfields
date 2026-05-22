<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class IncidentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        // 1. Siapkan query dasar
        $query = DB::table('incident_logs')
            ->join('users', 'incident_logs.reported_by', '=', 'users.id')
            ->select('incident_logs.*', 'users.full_name as reporter_name')
            ->where('incident_logs.is_deleted', false);

        // 2. CEK FILTER: Jika ada kiriman 'id' dari Dashboard, filter tabelnya!
        if ($request->has('id')) {
            $query->where('incident_logs.id', $request->id);
        }

        // 3. Eksekusi query dengan pagination
        $logs = $query->orderBy('incident_logs.created_at', 'desc')->paginate(15);

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
    public function updateStatus(Request $request, $id)
    {
        // 1. Validasi input status
        $request->validate(['status' => 'required|in:open,investigating,resolved']);
        $now = Carbon::now();

        // 2. Ambil data insiden yang lama
        $incident = DB::table('incident_logs')->where('id', $id)->first();
        if (!$incident) return back()->with('error', 'Data tidak ditemukan.');

        $oldStatus = $incident->status;
        $newStatus = $request->status;

        // 3. Jika statusnya memang berubah, lakukan Update & catat ke Audit
        if ($oldStatus != $newStatus) {
            // Update tabel insiden
            DB::table('incident_logs')->where('id', $id)->update([
                'status' => $newStatus,
                'updated_at' => $now,
            ]);

            // Catat otomatis ke Audit Trails
            DB::table('audit_trails')->insert([
                'incident_id' => $id,
                'user_id' => session('user_id'),
                'action' => 'UPDATED',
                'old_value' => 'Status awal: ' . strtoupper($oldStatus),
                'new_value' => 'Status diubah ke: ' . strtoupper($newStatus),
                'created_at' => $now,
            ]);
        }

        return back()->with('success', 'Status insiden berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $now = Carbon::now();
        $incident = DB::table('incident_logs')->where('id', $id)->first();
        if (!$incident) return back()->with('error', 'Data tidak ditemukan.');

        // 1. Lakukan Soft Delete (Ubah is_deleted jadi true)
        DB::table('incident_logs')->where('id', $id)->update([
            'is_deleted' => true,
            'updated_at' => $now,
        ]);

        // 2. Catat otomatis ke Audit Trails
        DB::table('audit_trails')->insert([
            'incident_id' => $id,
            'user_id' => session('user_id'),
            'action' => 'DELETED',
            'old_value' => 'Data Aktif (Severity: ' . $incident->severity_level . ')',
            'new_value' => 'Data dihapus (Soft Delete)',
            'created_at' => $now,
        ]);

        return back()->with('success', 'Log insiden berhasil dihapus dari sistem operasional!');
    }
}