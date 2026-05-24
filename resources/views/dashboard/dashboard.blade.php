@extends('layouts.app')

@section('page_title', 'Analytics Dashboard')

@section('content')
<!-- Filter -->
<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-lg font-bold text-gray-800">Ringkasan Operasional</h2>
        <p class="text-sm text-gray-500">Statistik insiden berdasarkan periode bulan.</p>
    </div>
    <form action="{{ route('dashboard') }}" method="GET">
        <input type="month" name="month" value="{{ $filterDate }}" onchange="this.form.submit()" 
               class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-[#1B4D3E] focus:border-[#1B4D3E]">
    </form>
</div>

<!-- Grid Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    @include('components.stat-card', ['label' => 'Total', 'value' => $totalInsiden, 'borderColor' => 'border-l-gray-800', 'textColor' => 'text-gray-800'])
    @include('components.stat-card', ['label' => 'Penanganan', 'value' => $sedangDiproses, 'borderColor' => 'border-l-red-600', 'textColor' => 'text-red-600'])
    @include('components.stat-card', ['label' => 'Verifikasi', 'value' => $menungguVerifikasi, 'borderColor' => 'border-l-blue-500', 'textColor' => 'text-blue-600'])
    @include('components.stat-card', ['label' => 'Selesai', 'value' => $selesai, 'borderColor' => 'border-l-green-500', 'textColor' => 'text-green-600'])
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4">Distribusi Tingkat Keparahan</h3>
        <div class="relative h-64"><canvas id="severityChart"></canvas></div>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h3 class="font-bold text-gray-700 mb-4">Rasio Status Penyelesaian</h3>
        <div class="relative h-64"><canvas id="statusChart"></canvas></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data dari PHP
    const sevData = @json(array_values($chartSeverity));
    const sevColors = ['#DC2626', '#F59E0B', '#10B981', '#9CA3AF']; // Critical(Merah), Medium(Kuning), Low(Hijau), Belum(Abu)

    // 1. Chart Severity (Bar)
    new Chart(document.getElementById('severityChart'), {
        type: 'bar',
        data: {
            labels: ['Critical', 'Medium', 'Low', 'Belum Diset'],
            datasets: [{
                label: 'Jumlah Insiden',
                data: sevData,
                backgroundColor: sevColors,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
        }
    });

    // 2. Chart Status (Doughnut)
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Diproses', 'Verifikasi', 'Selesai'],
            datasets: [{
                data: [{{ $sedangDiproses }}, {{ $menungguVerifikasi }}, {{ $selesai }}],
                backgroundColor: ['#DC2626', '#3B82F6', '#10B981']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>
@endsection