@extends('layouts.app')

@section('page_title', 'Incident Logs')

@section('content')

<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-8">
    <h2 class="text-lg font-bold text-gf-green mb-4">+ Lapor Insiden Baru</h2>
    
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
    @endif

    <form action="{{ route('incidents.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Insiden</label>
                <textarea name="description" rows="3" required maxlength="500" class="w-full border border-gray-300 rounded p-2 focus:border-[#1B4D3E]" placeholder="Jelaskan detail insiden (Maks. 500 karakter)"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Keparahan (Severity)</label>
                <select name="severity_level" required class="w-full border border-gray-300 rounded p-2 focus:border-[#1B4D3E]">
                    <option value="low">Low (Normal)</option>
                    <option value="medium">Medium (Peringatan)</option>
                    <option value="critical">Critical (Darurat)</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Detail</label>
            <textarea name="description" rows="3" required class="w-full border border-gray-300 rounded p-2 focus:border-[#1B4D3E]"></textarea>
        </div>
        <button type="submit" class="bg-[#1B4D3E] text-white px-4 py-2 rounded font-bold hover:bg-[#13382D] transition">Submit Log</button>
    </form>
</div>

<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mt-6 mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <form action="{{ route('incidents.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
        <div class="relative">
            <svg class="w-4 h-4 absolute left-3 top-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari insiden/pelapor..." class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#1B4D3E] focus:border-[#1B4D3E] w-full md:w-64">
        </div>
        <select name="severity" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-[#1B4D3E] focus:border-[#1B4D3E]">
            <option value="">Semua Severity</option>
            <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
            <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
        </select>
        <select name="status" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-[#1B4D3E] focus:border-[#1B4D3E]">
            <option value="">Semua Status</option>
            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Tertunda</option>
            <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Diproses</option>
            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
        </select>
        <div class="flex items-center space-x-2">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-[#1B4D3E] focus:border-[#1B4D3E]" title="Dari Tanggal">
            <span class="text-gray-400 font-bold">-</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-300 rounded-lg text-sm py-2 px-3 focus:ring-[#1B4D3E] focus:border-[#1B4D3E]" title="Sampai Tanggal">
        </div>
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
        @if(request()->anyFilled(['search', 'severity', 'status', 'start_date','end_date']))
            <a href="{{ route('incidents.index') }}" class="text-gray-500 hover:text-red-600 text-sm font-medium transition">Reset</a>
        @endif
    </form>
    <a href="{{ route('incidents.export') }}" class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm whitespace-nowrap shrink-0">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Export CSV
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mt-6">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <h3 class="font-bold text-gray-700 flex items-center">
            Daftar Log Insiden
            @if(request()->has('id'))
                <span class="ml-3 bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full border border-blue-200">
                    Filtered: ID #{{ request()->id }}
                </span>
            @endif
        </h3>
        
        @if(request()->has('id'))
            <a href="{{ route('incidents.index') }}" class="text-sm text-gray-500 hover:text-red-600 transition flex items-center bg-white border border-gray-200 hover:border-red-200 px-3 py-1.5 rounded-md shadow-sm">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Hapus Filter
            </a>
        @endif
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <th class="py-3 px-6 border-b">Tanggal</th>
                <th class="py-3 px-6 border-b">Dilaporkan Oleh</th>
                <th class="py-3 px-6 border-b">Judul</th>
                <th class="py-3 px-6 border-b">Severity</th>
                <th class="py-3 px-6 border-b">Status & Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @foreach($logs as $log)
            <tr class="border-b border-gray-50 hover:bg-gray-50">
                <td class="py-4 px-6 text-gray-500">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                <td class="py-4 px-6 text-gray-700">{{ $log->reporter_name }}</td>
                <td class="py-4 px-6 font-medium text-gray-800">{{ $log->incident_title }}</td>
                <td class="py-4 px-6">
                    @if($log->severity_level == 'critical')
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">CRITICAL</span>
                    @elseif($log->severity_level == 'medium')
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold">MEDIUM</span>
                    @else
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">LOW</span>
                    @endif
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center space-x-3">
                        @if(session('role') == 'supervisor')
                            <form action="{{ route('incidents.update_status', $log->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <select name="status" class="bg-gray-50 border border-gray-300 text-gray-700 text-xs rounded-lg focus:ring-[#1B4D3E] focus:border-[#1B4D3E] block p-2 transition font-bold uppercase" onchange="this.form.submit()">
                                    <option value="open" {{ $log->status == 'open' ? 'selected' : '' }}>TERTUNDA</option>
                                    <option value="investigating" {{ $log->status == 'investigating' ? 'selected' : '' }}>DIPROSES</option>
                                    <option value="resolved" {{ $log->status == 'resolved' ? 'selected' : '' }}>SELESAI</option>
                                </select>
                            </form>

                            <form action="{{ route('incidents.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Tindakan ini akan menghapus log insiden dari layar utama. Lanjutkan?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition bg-white hover:bg-red-50 p-1.5 rounded-md border border-transparent hover:border-red-200" title="Hapus Data">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        
                        @else
                            @if($log->status == 'open')
                                <span class="text-red-600 font-bold text-xs uppercase">TERTUNDA</span>
                            @elseif($log->status == 'investigating')
                                <span class="text-blue-600 font-bold text-xs uppercase">DIPROSES</span>
                            @else
                                <span class="text-green-600 font-bold text-xs uppercase">SELESAI</span>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 bg-gray-50">{{ $logs->links('pagination::tailwind') }}</div>
</div>
@endsection