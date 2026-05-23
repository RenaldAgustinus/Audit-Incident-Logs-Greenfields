<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Auth;

class IncidentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = DB::table('incident_logs')
            ->join('users', 'incident_logs.reported_by', '=', 'users.id')
            ->select('incident_logs.*', 'users.full_name as reporter_name')
            ->where('incident_logs.is_deleted', false);

        // Filter ID Spesifik (dari Dashboard)
        if ($request->has('id') && $request->id != '') {
            $query->where('incident_logs.id', $request->id);
        }

        // Fitur Pencarian (Kata Kunci Judul / Pelapor)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('incident_logs.incident_title', 'like', '%' . $request->search . '%')
                  ->orWhere('users.full_name', 'like', '%' . $request->search . '%');
            });
        }

        // Fitur Filter Status, Severity, Tanggal
        if ($request->filled('status')) {
            $query->where('incident_logs.status', $request->status);
        }
        if ($request->filled('severity')) {
            $query->where('incident_logs.severity_level', $request->severity);
        }
        // Fitur Filter Rentang Tanggal (Start Date & End Date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('incident_logs.created_at', '>=', $request->start_date)
                  ->whereDate('incident_logs.created_at', '<=', $request->end_date);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('incident_logs.created_at', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('incident_logs.created_at', '<=', $request->end_date);
        }

        // Ubah jadi 10 data per halaman dan ingat query filternya
        $logs = $query->orderBy('incident_logs.created_at', 'desc')->paginate(10)->withQueryString();

        return view('incident.incident', compact('logs'));
    }

    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'incident_title' => 'required|string|max:150',
            'description'    => 'required|string',
        ]);

        // 2. Simpan ke database
        $incidentId = DB::table('incident_logs')->insertGetId([
            'reported_by'    => session('user_id'), // <--- UBAH BAGIAN INI
            'incident_title' => $request->incident_title,
            'description'    => $request->description,
            'status'         => 'insiden_baru', 
            'created_at'     => now(),
        ]);

        // 3. Catat ke Audit Trail
        AuditLogger::log(
            $incidentId, 
            'CREATE', 
            'Operator menambah insiden baru'
        );

        return redirect()->back()->with('success', 'Insiden berhasil dilaporkan dan menunggu evaluasi Supervisor.');
    }

    /**
     * FLOW 2: SUPERVISOR MENENTUKAN SEVERITY
     */
    public function setSeverity(Request $request, $id)
    {
        // 1. Validasi pilihan severity
        $request->validate([
            'severity_level' => 'required|in:low,medium,critical'
        ]);

        // 2. Update status & severity di database
        DB::table('incident_logs')
            ->where('id', $id)
            ->update([
                'severity_level' => $request->severity_level,
                'status'         => 'butuh_tindak_lanjut', // Berubah status
                'updated_at'     => now(),
            ]);

        // 3. Catat ke Audit Trail
        AuditLogger::log(
            $id, 
            'UPDATE_SEVERITY', 
            'Supervisor menetapkan tingkat severity menjadi ' . strtoupper($request->severity_level)
        );

        return redirect()->back()->with('success', 'Severity ditetapkan. Insiden dikembalikan ke Operator untuk ditindaklanjuti.');
    }

    public function destroy($id)
    {
        if (session('role') !== 'supervisor') {
            return back()->with('error', 'Akses ditolak! Hanya Supervisor yang memiliki wewenang untuk menghapus data.');
        }

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
    public function exportCsv(\Illuminate\Http\Request $request)
    {
        $logs = DB::table('incident_logs')
            ->join('users', 'incident_logs.reported_by', '=', 'users.id')
            ->select('incident_logs.*', 'users.full_name as reporter_name')
            ->where('incident_logs.is_deleted', false)
            ->orderBy('incident_logs.created_at', 'desc')
            ->get();

        $fileName = 'Greenfields_Incident_Logs_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($logs) {
            $file = fopen('php://output', 'w');
            // Header Kolom Excel
            fputcsv($file, ['ID Insiden', 'Tanggal', 'Dilaporkan Oleh', 'Judul Insiden', 'Tingkat Keparahan', 'Status', 'Deskripsi Lengkap']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    '#GF-'.date('Y', strtotime($log->created_at)).'-'.str_pad($log->id, 3, '0', STR_PAD_LEFT),
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
    public function resolveIncident(Request $request, $id)
    {
        // 1. Validasi input form (Wajib isi catatan, foto opsional/wajib tergantung kebutuhan, di sini kita set wajib)
        $request->validate([
            'resolution_notes' => 'required|string',
            'resolution_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        // 2. Proses Upload Foto (kalau ada)
        $photoPath = null;
        if ($request->hasFile('resolution_photo')) {
            // Simpan ke folder storage/app/public/resolutions
            $photoPath = $request->file('resolution_photo')->store('resolutions', 'public');
        }

        // 3. Update database
        DB::table('incident_logs')
            ->where('id', $id)
            ->update([
                'resolution_notes' => $request->resolution_notes,
                'resolution_photo' => $photoPath,
                'status'           => 'menunggu_verifikasi', // Status maju ke Supervisor
                'updated_at'       => now(),
            ]);

        // 4. Catat di Audit Trail
        AuditLogger::log(
            $id,
            'RESOLVE',
            'Operator mengirim laporan tindak lanjut dan foto bukti'
        );

        return redirect()->back()->with('success', 'Tindak lanjut berhasil dikirim. Menunggu verifikasi Supervisor.');
    }

    /**
     * FLOW 5: SUPERVISOR VERIFIKASI (APPROVE / REJECT)
     */
    public function verifyIncident(Request $request, $id)
    {
        // 1. Validasi pilihan aksi
        $request->validate([
            'verification_action' => 'required|in:approve,reject'
        ]);

        $statusBaru = $request->verification_action === 'approve' ? 'selesai' : 'butuh_tindak_lanjut';
        $keteranganLog = $request->verification_action === 'approve' 
            ? 'Supervisor menyetujui tindak lanjut (Status: Selesai)' 
            : 'Supervisor menolak tindak lanjut. Dikembalikan ke Operator';

        // 2. Update status
        DB::table('incident_logs')
            ->where('id', $id)
            ->update([
                'status'     => $statusBaru,
                'updated_at' => now(),
            ]);

        // 3. Catat di Audit Trail
        AuditLogger::log(
            $id,
            'VERIFY',
            $keteranganLog
        );

        return redirect()->back()->with('success', 'Verifikasi berhasil disimpan.');
    }
}