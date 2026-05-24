<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // CLEAN CODE 1: Gunakan Carbon untuk manipulasi tanggal yang lebih aman dan rapi
        $filterDate = $request->input('month', now()->format('Y-m')); 
        $date = Carbon::parse($filterDate);

        // 2. Base Query berdasarkan bulan & tahun terpilih
        $query = DB::table('incident_logs')
            ->where('is_deleted', false)
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month);

        // 3. Data untuk Cards (Kartu Metrik) - TEKNIK (clone) KAMU SUDAH SANGAT SEMPURNA!
        $totalInsiden       = (clone $query)->count();
        $menungguVerifikasi = (clone $query)->where('status', 'menunggu_verifikasi')->count();
        $sedangDiproses     = (clone $query)->whereIn('status', ['insiden_baru', 'butuh_tindak_lanjut'])->count();
        $selesai            = (clone $query)->where('status', 'selesai')->count();

        // 4. Data untuk Grafik (Chart.js)
        $severityData = (clone $query)
            ->select('severity_level', DB::raw('count(*) as total'))
            ->groupBy('severity_level')
            ->pluck('total', 'severity_level'); // Pluck otomatis mengembalikan Laravel Collection

        // CLEAN CODE 2: Gunakan method ->get() bawaan Collection Laravel untuk default value
        // Ini lebih elegan daripada menggunakan array key fallback (?? 0)
        $chartSeverity = [
            'Critical'    => $severityData->get('critical', 0),
            'Medium'      => $severityData->get('medium', 0),
            'Low'         => $severityData->get('low', 0),
            'Belum Diset' => $severityData->get('', 0), 
        ];

        return view('dashboard.dashboard', compact(
            'filterDate', 'totalInsiden', 'menungguVerifikasi', 'sedangDiproses', 'selesai', 'chartSeverity'
        ));
    }
}