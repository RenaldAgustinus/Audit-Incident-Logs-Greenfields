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
                <input type="text" name="incident_title" required class="w-full border border-gray-300 rounded p-2 focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E]">
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

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <th class="py-3 px-6 border-b">Tanggal</th>
                <th class="py-3 px-6 border-b">Dilaporkan Oleh</th>
                <th class="py-3 px-6 border-b">Judul</th>
                <th class="py-3 px-6 border-b">Severity</th>
                <th class="py-3 px-6 border-b">Status</th>
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
                <td class="py-4 px-6 uppercase text-xs font-bold">{{ $log->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 bg-gray-50">{{ $logs->links('pagination::tailwind') }}</div>
</div>
@endsection