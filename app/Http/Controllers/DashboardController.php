<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Filter Bulan (Default ke bulan berjalan)
        $filterDate = $request->input('month', date('Y-m')); 
        $year = date('Y', strtotime($filterDate));
        $month = date('m', strtotime($filterDate));

        // 2. Base Query berdasarkan bulan & tahun terpilih
        $query = DB::table('incident_logs')
            ->where('is_deleted', false)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month);

        // 3. Data untuk Cards (Kartu Metrik)
        $totalInsiden = (clone $query)->count();
        $menungguVerifikasi = (clone $query)->where('status', 'menunggu_verifikasi')->count();
        $sedangDiproses = (clone $query)->whereIn('status', ['insiden_baru', 'butuh_tindak_lanjut'])->count();
        $selesai = (clone $query)->where('status', 'selesai')->count();

        // 4. Data untuk Grafik (Chart.js) Berdasarkan Tingkat Keparahan
        $severityData = (clone $query)
            ->select('severity_level', DB::raw('count(*) as total'))
            ->groupBy('severity_level')
            ->pluck('total', 'severity_level')
            ->toArray();

        // Antisipasi kalau data kosong
        $chartSeverity = [
            'Critical' => $severityData['critical'] ?? 0,
            'Medium'   => $severityData['medium'] ?? 0,
            'Low'      => $severityData['low'] ?? 0,
            'Belum Diset' => $severityData[''] ?? 0, // Untuk insiden_baru yang belum dinilai
        ];

        return view('dashboard.dashboard', compact(
            'filterDate', 'totalInsiden', 'menungguVerifikasi', 'sedangDiproses', 'selesai', 'chartSeverity'
        ));
    }
}