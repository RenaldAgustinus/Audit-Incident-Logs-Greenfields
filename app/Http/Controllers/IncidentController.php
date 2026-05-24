<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;

class IncidentController extends Controller
{
    /**
     * Tampilkan antarmuka daftar insiden (termasuk fitur filter & pencarian)
     */
    public function index(Request $request)
    {
        $logs = DB::table('incident_logs')
            ->join('users', 'incident_logs.reported_by', '=', 'users.id')
            ->select('incident_logs.*', 'users.full_name as reporter_name')
            ->where('incident_logs.is_deleted', false)
            
            // CLEAN CODE: Menggunakan metode when() untuk menggantikan tumpukan IF
            ->when($request->filled('id'), fn($q) => $q->where('incident_logs.id', $request->id))
            ->when($request->filled('status'), fn($q) => $q->where('incident_logs.status', $request->status))
            ->when($request->filled('severity'), fn($q) => $q->where('incident_logs.severity_level', $request->severity))
            ->when($request->filled('start_date'), fn($q) => $q->whereDate('incident_logs.created_at', '>=', $request->start_date))
            ->when($request->filled('end_date'), fn($q) => $q->whereDate('incident_logs.created_at', '<=', $request->end_date))
            ->when($request->filled('search'), fn($q) => 
                $q->where(fn($subQuery) => 
                    $subQuery->where('incident_logs.incident_title', 'like', "%{$request->search}%")
                             ->orWhere('users.full_name', 'like', "%{$request->search}%")
                )
            )
            ->orderBy('incident_logs.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('incident.incident', compact('logs'));
    }

    /**
     * FLOW 1: OPERATOR LAPOR INSIDEN
     */
    public function store(Request $request)
    {
        $request->validate([
            'incident_title' => 'required|string|max:150',
            'description'    => 'required|string',
        ]);

        $incidentId = DB::table('incident_logs')->insertGetId([
            'reported_by'    => session('user_id'),
            'incident_title' => $request->incident_title,
            'description'    => $request->description,
            'status'         => 'insiden_baru', 
            'created_at'     => now(),
        ]);

        AuditLogger::log($incidentId, 'CREATE', 'Operator menambah insiden baru');

        return back()->with('success', 'Insiden berhasil dilaporkan dan menunggu evaluasi Supervisor.');
    }

    /**
     * FLOW 2: SUPERVISOR MENENTUKAN SEVERITY
     */
    public function setSeverity(Request $request, $id)
    {
        $request->validate(['severity_level' => 'required|in:low,medium,critical']);

        DB::table('incident_logs')->where('id', $id)->update([
            'severity_level' => $request->severity_level,
            'status'         => 'butuh_tindak_lanjut',
            'updated_at'     => now(),
        ]);

        AuditLogger::log($id, 'UPDATE_SEVERITY', 'Supervisor menetapkan tingkat severity menjadi ' . strtoupper($request->severity_level));

        return back()->with('success', 'Severity ditetapkan. Insiden dikembalikan ke Operator untuk ditindaklanjuti.');
    }

    /**
     * FLOW 3: OPERATOR TINDAK LANJUT & UPLOAD FOTO
     */
    public function resolveIncident(Request $request, $id)
    {
        $request->validate([
            'resolution_notes' => 'required|string',
            'resolution_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // CLEAN CODE: Nullsafe operator PHP 8 (?->) untuk mempersingkat logika if(hasFile)
        $photoPath = $request->file('resolution_photo')?->store('resolutions', 'public');

        DB::table('incident_logs')->where('id', $id)->update([
            'resolution_notes' => $request->resolution_notes,
            'resolution_photo' => $photoPath,
            'status'           => 'menunggu_verifikasi',
            'updated_at'       => now(),
        ]);

        AuditLogger::log($id, 'RESOLVE', 'Operator mengirim laporan tindak lanjut dan foto bukti');

        return back()->with('success', 'Tindak lanjut berhasil dikirim. Menunggu verifikasi Supervisor.');
    }

    /**
     * FLOW 4: SUPERVISOR VERIFIKASI
     */
    public function verifyIncident(Request $request, $id)
    {
        $request->validate(['verification_action' => 'required|in:approve,reject']);

        // CLEAN CODE: Menggunakan boolean flag untuk mempersingkat ternary operator
        $isApproved = $request->verification_action === 'approve';

        DB::table('incident_logs')->where('id', $id)->update([
            'status'     => $isApproved ? 'selesai' : 'butuh_tindak_lanjut',
            'updated_at' => now(),
        ]);

        AuditLogger::log(
            $id, 
            'VERIFY', 
            $isApproved ? 'Supervisor menyetujui tindak lanjut (Status: Selesai)' : 'Supervisor menolak tindak lanjut. Dikembalikan ke Operator'
        );

        return back()->with('success', 'Verifikasi berhasil disimpan.');
    }

    /**
     * FLOW 5: SUPERVISOR HAPUS DATA (SOFT DELETE)
     */
    public function destroy($id)
    {
        if (session('role') !== 'supervisor') {
            return back()->with('error', 'Akses ditolak! Hanya Supervisor yang memiliki wewenang.');
        }

        $incident = DB::table('incident_logs')->where('id', $id)->first();
        if (!$incident) return back()->with('error', 'Data tidak ditemukan.');

        DB::table('incident_logs')->where('id', $id)->update([
            'is_deleted' => true,
            'updated_at' => now(),
        ]);

        DB::table('audit_trails')->insert([
            'incident_id' => $id,
            'user_id'     => session('user_id'),
            'action'      => 'DELETED',
            'old_value'   => 'Data Aktif (Severity: ' . $incident->severity_level . ')',
            'new_value'   => 'Data dihapus (Soft Delete)',
            'created_at'  => now(),
        ]);

        return back()->with('success', 'Log insiden berhasil dihapus dari sistem operasional!');
    }

    /**
     * FITUR TAMBAHAN: EXPORT CSV
     */
    public function exportCsv(Request $request)
    {
        $logs = DB::table('incident_logs')
            ->join('users', 'incident_logs.reported_by', '=', 'users.id')
            ->select('incident_logs.*', 'users.full_name as reporter_name')
            ->where('incident_logs.is_deleted', false)
            ->orderBy('incident_logs.created_at', 'desc')
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=Greenfields_Incident_Logs_" . date('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Insiden', 'Tanggal', 'Dilaporkan Oleh', 'Judul Insiden', 'Tingkat Keparahan', 'Status', 'Deskripsi Lengkap']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    '#GF-' . date('Y', strtotime($log->created_at)) . '-' . str_pad($log->id, 3, '0', STR_PAD_LEFT),
                    $log->created_at,
                    $log->reporter_name,
                    $log->incident_title,
                    strtoupper($log->severity_level),
                    strtoupper($log->status),
                    $log->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}