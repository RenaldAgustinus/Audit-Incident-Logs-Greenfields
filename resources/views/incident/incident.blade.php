@extends('layouts.app')

@section('page_title', 'Incident Logs')

@section('content')

<!-- Notifikasi Sukses -->
@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-6 flex justify-between items-center">
        <div>
            <span class="font-bold">Berhasil!</span> {{ session('success') }}
        </div>
        <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
@endif

<!-- CONTROL PANEL: Search, Filter, Export, Add New -->
<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
    
    <!-- Bagian Kiri: Search & Filter Dropdown -->
    <form action="{{ route('incidents.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
        <!-- Search Bar -->
        <div class="relative w-full md:w-64">
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari insiden/pelapor..." class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#1B4D3E] focus:border-[#1B4D3E] w-full" onchange="this.form.submit()">
        </div>
        
        <!-- Filter Dropdown Container -->
        <div class="relative">
            <button type="button" onclick="toggleDropdown('filterDropdown')" class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                Filter Data
            </button>
            
            <!-- Dropdown Menu -->
            <div id="filterDropdown" class="hidden absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-100 p-5 z-20">
                <h4 class="text-xs font-bold text-gray-500 uppercase mb-4 tracking-wider">Filter Lanjutan</h4>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-600 block mb-1 font-bold">Tingkat Keparahan (Severity)</label>
                        <select name="severity" class="w-full border border-gray-300 rounded-md text-sm py-2 px-3 focus:ring-[#1B4D3E]">
                            <option value="">Semua Severity</option>
                            <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                            <option value="medium" {{ request('severity') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('severity') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1 font-bold">Status Penyelesaian</label>
                        <select name="status" class="w-full border border-gray-300 rounded-md text-sm py-2 px-3 focus:ring-[#1B4D3E]">
                            <option value="">Semua Status</option>
                            <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Tertunda</option>
                            <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Diproses</option>
                            <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600 block mb-1 font-bold">Rentang Waktu</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border border-gray-300 rounded-md text-xs py-2 px-2 focus:ring-[#1B4D3E]">
                            <span class="text-gray-400 font-bold">-</span>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border border-gray-300 rounded-md text-xs py-2 px-2 focus:ring-[#1B4D3E]">
                        </div>
                    </div>
                    <div class="pt-4 border-t border-gray-100 flex justify-end gap-3 items-center">
                        @if(request()->anyFilled(['search', 'severity', 'status', 'start_date','end_date']))
                            <a href="{{ route('incidents.index') }}" class="text-xs text-red-600 hover:text-red-800 font-bold transition">Reset Filter</a>
                        @endif
                        <button type="submit" class="bg-[#1B4D3E] hover:bg-[#13382D] text-white text-xs font-bold px-4 py-2 rounded transition">Terapkan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Bagian Kanan: Export & Lapor Insiden -->
    <div class="flex items-center gap-3 w-full md:w-auto justify-end">
        <a href="{{ route('incidents.export') }}" class="flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export CSV
        </a>
        <button onclick="toggleModal('addIncidentModal')" class="flex items-center bg-[#1B4D3E] hover:bg-[#13382D] text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Lapor Insiden
        </button>
    </div>
</div>

<!-- TABEL UTAMA -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
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

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[800px]">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="py-3 px-6 border-b">Tanggal</th>
                    <th class="py-3 px-6 border-b">Dilaporkan Oleh</th>
                    <th class="py-3 px-6 border-b">Judul</th>
                    <th class="py-3 px-6 border-b">Severity</th>
                    <th class="py-3 px-6 border-b text-center">Status & Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($logs as $log)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="py-4 px-6 text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                    <td class="py-4 px-6 text-gray-700 font-medium">{{ $log->reporter_name }}</td>
                    <td class="py-4 px-6 font-bold text-gray-800">{{ $log->incident_title }}</td>
                    <td class="py-4 px-6">
                        @if($log->severity_level == 'critical')
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold flex items-center w-max"><span class="w-2 h-2 rounded-full bg-red-600 mr-2 animate-pulse"></span>CRITICAL</span>
                        @elseif($log->severity_level == 'medium')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold w-max block">MEDIUM</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold w-max block">LOW</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center space-x-3">
                            @if(session('role') == 'supervisor')
                                <form action="{{ route('incidents.update_status', $log->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="bg-white border border-gray-300 text-gray-700 text-xs rounded-lg focus:ring-[#1B4D3E] focus:border-[#1B4D3E] block p-2 font-bold uppercase shadow-sm cursor-pointer" onchange="this.form.submit()">
                                        <option value="open" {{ $log->status == 'open' ? 'selected' : '' }}>TERTUNDA</option>
                                        <option value="investigating" {{ $log->status == 'investigating' ? 'selected' : '' }}>DIPROSES</option>
                                        <option value="resolved" {{ $log->status == 'resolved' ? 'selected' : '' }}>SELESAI</option>
                                    </select>
                                </form>

                                <form action="{{ route('incidents.destroy', $log->id) }}" method="POST" class="m-0" onsubmit="return confirm('Tindakan ini akan menghapus log insiden dari layar utama. Lanjutkan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition bg-white hover:bg-red-50 p-2 rounded-md border border-gray-200 hover:border-red-200 shadow-sm" title="Hapus Data (Soft Delete)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            @else
                                @if($log->status == 'open')
                                    <span class="text-red-600 font-bold text-xs uppercase bg-red-50 px-3 py-1 rounded-full border border-red-100">TERTUNDA</span>
                                @elseif($log->status == 'investigating')
                                    <span class="text-blue-600 font-bold text-xs uppercase bg-blue-50 px-3 py-1 rounded-full border border-blue-100">DIPROSES</span>
                                @else
                                    <span class="text-green-600 font-bold text-xs uppercase bg-green-50 px-3 py-1 rounded-full border border-green-100">SELESAI</span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="font-medium text-lg">Tidak ada data insiden ditemukan.</p>
                        <p class="text-sm">Coba sesuaikan kata kunci atau filter pencarian Anda.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 bg-gray-50 border-t border-gray-100">{{ $logs->links('pagination::tailwind') }}</div>
</div>

<!-- ========================================= -->
<!-- MODAL OVERLAY: FORM LAPOR INSIDEN BARU  -->
<!-- ========================================= -->
<div id="addIncidentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex justify-center items-center backdrop-blur-sm transition-opacity">
    
    <!-- Modal Content -->
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="bg-[#1B4D3E] px-6 py-4 flex justify-between items-center">
            <h2 class="text-white font-bold text-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Form Lapor Insiden Baru
            </h2>
            <button type="button" onclick="toggleModal('addIncidentModal')" class="text-green-100 hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <form action="{{ route('incidents.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Judul Insiden <span class="text-red-500">*</span></label>
                        <input type="text" name="incident_title" required maxlength="150" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#1B4D3E] focus:border-[#1B4D3E] transition" placeholder="Cth: Pipa chiller bocor di Sektor B">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tingkat Keparahan (Severity) <span class="text-red-500">*</span></label>
                        <select name="severity_level" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-[#1B4D3E] focus:border-[#1B4D3E] transition">
                            <option value="low">Low (Normal)</option>
                            <option value="medium">Medium (Peringatan)</option>
                            <option value="critical">Critical (Darurat)</option>
                        </select>
                    </div>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Detail <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-[#1B4D3E] focus:border-[#1B4D3E] transition" placeholder="Jelaskan kronologi dan detail insiden di sini..."></textarea>
                </div>
                
                <!-- Modal Footer -->
                <div class="flex justify-end items-center space-x-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="toggleModal('addIncidentModal')" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-bold text-white bg-[#1B4D3E] hover:bg-[#13382D] rounded-lg shadow-md transition">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================= -->
<!-- JAVASCRIPT UNTUK MODAL & DROPDOWN         -->
<!-- ========================================= -->
<script>
    // Fungsi Toggle Modal Form
    function toggleModal(modalID) {
        const modal = document.getElementById(modalID);
        modal.classList.toggle('hidden');
    }

    // Fungsi Toggle Dropdown Filter
    function toggleDropdown(dropdownID) {
        const dropdown = document.getElementById(dropdownID);
        dropdown.classList.toggle('hidden');
    }
    
    // Auto-close dropdown kalau klik di luar area filter
    window.onclick = function(event) {
        if (!event.target.closest('.relative')) {
            const dropdowns = document.querySelectorAll('[id^="filterDropdown"]');
            dropdowns.forEach(dropdown => {
                if (!dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    }
</script>

@endsection