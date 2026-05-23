@extends('layouts.app')

@section('page_title', 'System Audit Trails')

@section('content')
<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6">
    <h2 class="text-lg font-bold text-gray-800">Rekam Jejak Operasional</h2>
    <p class="text-sm text-gray-500">Pilih insiden untuk melihat riwayat aktivitas dan perubahan statusnya.</p>
</div>

<!-- TABEL LIST INSIDEN -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                <th class="py-3 px-6 border-b">ID Insiden</th>
                <th class="py-3 px-6 border-b">Tanggal Masuk</th>
                <th class="py-3 px-6 border-b">Judul Insiden</th>
                <th class="py-3 px-6 border-b">Status Terkini</th>
                <th class="py-3 px-6 border-b text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            @forelse($incidents as $incident)
            <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                <td class="py-4 px-6 font-bold text-gray-600">#GF-{{ str_pad($incident->id, 4, '0', STR_PAD_LEFT) }}</td>
                <td class="py-4 px-6 text-gray-500">{{ \Carbon\Carbon::parse($incident->created_at)->format('d M Y, H:i') }}</td>
                <td class="py-4 px-6 font-medium text-gray-800">{{ $incident->incident_title }}</td>
                <td class="py-4 px-6">
                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold uppercase">{{ str_replace('_', ' ', $incident->status) }}</span>
                </td>
                <td class="py-4 px-6 text-center">
                    <!-- Lempar Data Audit Trail per Baris ke JavaScript -->
                    <button 
                        onclick="openAuditModal({{ $incident->id }}, '{{ $incident->incident_title }}', {{ json_encode($trails[$incident->id] ?? []) }})"
                        class="bg-[#1B4D3E] hover:bg-[#13382D] text-white px-4 py-2 rounded-lg text-xs font-bold transition flex items-center justify-center mx-auto shadow-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Lihat Jejak
                    </button>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-10 text-center text-gray-500">Tidak ada rekam jejak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 bg-gray-50 border-t border-gray-100">{{ $incidents->links('pagination::tailwind') }}</div>
</div>

<!-- ========================================= -->
<!-- MODAL AUDIT TIMELINE                      -->
<!-- ========================================= -->
<div id="auditModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden transform transition-all flex flex-col max-h-[85vh]">
        
        <!-- Header Modal -->
        <div class="bg-gray-800 px-6 py-4 flex justify-between items-center shrink-0">
            <div>
                <h2 class="text-white font-bold text-lg flex items-center">Riwayat Aktivitas Insiden</h2>
                <p id="modalIncidentTitle" class="text-gray-300 text-sm mt-1"></p>
            </div>
            <button onclick="document.getElementById('auditModal').classList.add('hidden')" class="text-gray-400 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Body Modal (Bisa di-scroll) -->
        <div class="p-6 overflow-y-auto flex-1 bg-gray-50">
            <div class="relative border-l-2 border-green-500 ml-3" id="timelineContainer">
                <!-- Timeline items akan dirender pakai JavaScript di sini -->
            </div>
        </div>
        
        <div class="p-4 border-t border-gray-200 bg-white shrink-0 flex justify-end">
             <button onclick="document.getElementById('auditModal').classList.add('hidden')" class="px-5 py-2 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Tutup Panel</button>
        </div>
    </div>
</div>

<!-- JAVASCRIPT UNTUK RENDER TIMELINE -->
<script>
    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function openAuditModal(id, title, trails) {
        // Set Judul
        document.getElementById('modalIncidentTitle').innerText = `#GF-${String(id).padStart(4, '0')} - ${title}`;
        
        const container = document.getElementById('timelineContainer');
        container.innerHTML = ''; // Bersihkan timeline sebelumnya

        if (trails.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-sm ml-6 italic">Belum ada aktivitas yang terekam.</p>';
        } else {
            // Looping data audit trail
            trails.forEach(trail => {
                let badgeColor = 'bg-blue-100 text-blue-700';
                if(trail.action === 'CREATE') badgeColor = 'bg-purple-100 text-purple-700';
                if(trail.action === 'UPDATE_SEVERITY') badgeColor = 'bg-orange-100 text-orange-700';
                if(trail.action === 'RESOLVE') badgeColor = 'bg-yellow-100 text-yellow-700';
                if(trail.action === 'VERIFY' || trail.action === 'APPROVE') badgeColor = 'bg-green-100 text-green-700';
                
                const itemHtml = `
                    <div class="mb-8 ml-8 relative">
                        <span class="absolute -left-[41px] top-1 w-5 h-5 bg-green-500 rounded-full border-4 border-white shadow"></span>
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="text-xs font-bold ${badgeColor} px-2 py-0.5 rounded uppercase">${trail.action}</span>
                                    <span class="text-xs text-gray-500 ml-2 font-medium">${formatTime(trail.created_at)}</span>
                                </div>
                            </div>
                            <p class="text-gray-800 text-sm font-medium mb-2">${trail.description}</p>
                            <div class="flex items-center text-xs text-gray-500 bg-gray-50 p-2 rounded border border-gray-100">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span class="font-bold text-gray-700 mr-1">${trail.actor_name}</span> 
                                <span class="uppercase">(${trail.actor_role})</span>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', itemHtml);
            });
        }

        // Tampilkan Modal
        document.getElementById('auditModal').classList.remove('hidden');
    }
</script>
@endsection