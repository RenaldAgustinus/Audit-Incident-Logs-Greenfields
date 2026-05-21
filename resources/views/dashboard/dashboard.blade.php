@extends('layouts.app')

@section('page_title', 'Overview Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Total Insiden</h3>
        <p class="text-3xl font-bold text-gf-green">{{ $totalIncidents }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Critical Open</h3>
        <p class="text-3xl font-bold text-red-600">{{ $criticalOpenCount }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">System Status</h3>
        <p class="text-3xl font-bold text-green-600">Normal</p>
    </div>
</div>

@if($criticalIncidents->count() > 0)
<div class="mb-8">
    <h2 class="text-lg font-bold text-red-700 flex items-center mb-4">
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        URGENT ACTION REQUIRED
    </h2>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($criticalIncidents as $incident)
        <div class="bg-white border-l-4 border-red-600 rounded-lg shadow-sm p-6 relative">
            <div class="absolute top-6 right-6">
                <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded">CRITICAL</span>
            </div>
            <div class="flex items-start">
                <div class="bg-red-50 p-3 rounded mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $incident->incident_title }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($incident->description, 90) }}</p>
                    <p class="text-xs text-gray-400 mb-4">Dilaporkan: {{ \Carbon\Carbon::parse($incident->created_at)->format('d M Y, H:i') }} WIB</p>
                    <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm transition">
                        Tindak Lanjuti &rarr;
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-gf-green flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            RECENT LOGS
        </h2>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="py-3 px-6 border-b">ID</th>
                    <th class="py-3 px-6 border-b">Judul Insiden</th>
                    <th class="py-3 px-6 border-b">Dilaporkan Oleh</th>
                    <th class="py-3 px-6 border-b">Tanggal</th>
                    <th class="py-3 px-6 border-b">Status</th>
                    <th class="py-3 px-6 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($recentLogs as $log)
                <tr class="hover:bg-gray-50 border-b border-gray-50 last:border-0 transition">
                    <td class="py-4 px-6 font-medium text-gray-700">#GF-{{ date('Y') }}-{{ str_pad($log->id, 3, '0', STR_PAD_LEFT) }}</td>
                    <td class="py-4 px-6 text-gray-800">{{ $log->incident_title }}</td>
                    <td class="py-4 px-6 text-gray-600">{{ $log->reporter_name }}</td>
                    <td class="py-4 px-6 text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                    <td class="py-4 px-6">
                        @if($log->status == 'open')
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">TERTUNDA</span>
                        @elseif($log->status == 'investigating')
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">DIPROSES</span>
                        @else
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">SELESAI</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center">
                        <button class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="p-4 bg-gray-50 border-t border-gray-100">
            {{ $recentLogs->links('pagination::tailwind') }}
        </div>
    </div>
</div>
@endsection