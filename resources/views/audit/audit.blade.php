@extends('layouts.app')

@section('page_title', 'System Audit Trails')

@section('content')

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h3 class="font-bold text-gray-700 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
            Immutable Logs
        </h3>
    </div>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-white text-gray-500 text-xs uppercase tracking-wider">
                <th class="py-3 px-6 border-b">Timestamp</th>
                <th class="py-3 px-6 border-b">User (Role)</th>
                <th class="py-3 px-6 border-b">Aksi</th>
                <th class="py-3 px-6 border-b">Detail Insiden</th>
                <th class="py-3 px-6 border-b">Keterangan Tambahan</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @foreach($audits as $audit)
            <tr class="border-b border-gray-50 hover:bg-gray-50">
                <td class="py-4 px-6 text-gray-500 font-mono text-xs">{{ $audit->created_at }}</td>
                <td class="py-4 px-6">
                    <span class="font-bold text-gray-700">{{ $audit->full_name }}</span><br>
                    <span class="text-xs text-gray-400 capitalize">{{ $audit->role }}</span>
                </td>
                <td class="py-4 px-6">
                    <span class="bg-[#1B4D3E] text-white px-2 py-1 rounded text-xs font-bold">{{ $audit->action }}</span>
                </td>
                <td class="py-4 px-6 text-gray-600">{{ $audit->incident_title }}</td>
                <td class="py-4 px-6 text-gray-500">{{ $audit->new_value }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4 bg-white">{{ $audits->links('pagination::tailwind') }}</div>
</div>
@endsection