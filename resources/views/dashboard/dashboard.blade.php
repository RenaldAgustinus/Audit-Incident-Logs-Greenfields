@extends('layouts.app')

@section('page_title', 'Analytics Dashboard')

@section('content')
<!-- Filter Rentang Bulan -->
<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-bold text-gray-800">Ringkasan Operasional</h2>
        <p class="text-sm text-gray-500">Statistik insiden berdasarkan periode bulan.</p>
    </div>
    <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2">
        <label class="text-sm font-bold text-gray-600">Pilih Bulan:</label>
        <input type="month" name="month" value="{{ $filterDate }}" onchange="this.form.submit()" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-[#1B4D3E] focus:border-[#1B4D3E]">
    </form>
</div>

<!-- Grid Cards (Metrik Cepat) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-gray-800">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Total Insiden Bulan Ini</h3>
        <p class="text-3xl font-bold text-gray-800">{{ $totalInsiden }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-red-600">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Butuh Penanganan</h3>
        <p class="text-3xl font-bold text-red-600">{{ $sedangDiproses }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-blue-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Menunggu Verifikasi</h3>
        <p class="text-3xl font-bold text-blue-600">{{ $menungguVerifikasi }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-l-4 border-l-green-500">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Insiden Selesai</h3>
        <p class="text-3xl font-bold text-green-600">{{ $selesai }}</p>
    </div>
</div>

<!-- Area Diagram / Chart -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Chart 1: Bar Chart Severity -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4">Distribusi Tingkat Keparahan (Severity)</h3>
        <div class="relative h-64 w-full">
            <canvas id="severityChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Doughnut Chart Status -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col items-center">
        <h3 class="font-bold text-gray-700 mb-4 w-full text-left">Rasio Status Penyelesaian</h3>
        <div class="relative h-64 w-64">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>

<!-- Load Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Konfigurasi Chart Severity (Bar)
    const ctxSev = document.getElementById('severityChart').getContext('2d');
    new Chart(ctxSev, {
        type: 'bar',
        data: {
            labels: ['Critical', 'Medium', 'Low', 'Belum Diset'],
            datasets: [{
                label: 'Jumlah Insiden',
                data: [
                    {{ $chartSeverity['Critical'] }},
                    {{ $chartSeverity['Medium'] }},
                    {{ $chartSeverity['Low'] }},
                    {{ $chartSeverity['Belum Diset'] }}
                ],
                backgroundColor: ['#DC2626', '#D97706', '#9CA3AF', '#E5E7EB'], // Merah, Kuning, Abu, Abu Terang
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // Konfigurasi Chart Status (Doughnut)
    const ctxStat = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStat, {
        type: 'doughnut',
        data: {
            labels: ['Diproses', 'Verifikasi', 'Selesai'],
            datasets: [{
                data: [{{ $sedangDiproses }}, {{ $menungguVerifikasi }}, {{ $selesai }}],
                backgroundColor: ['#DC2626', '#3B82F6', '#10B981'], // Merah, Biru, Hijau
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
@endsection