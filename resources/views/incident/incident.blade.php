@extends('layouts.app')

@section('page_title', 'Incident Logs')

@section('content')

<!-- Notifikasi -->
@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm mb-6 flex justify-between items-center">
        <div><span class="font-bold">Berhasil!</span> {{ session('success') }}</div>
        <button onclick="this.parentElement.style.display='none'" class="text-green-700 hover:text-green-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
@endif
@if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm mb-6 flex justify-between items-center">
        <div><span class="font-bold">Gagal!</span> {{ session('error') }}</div>
        <button onclick="this.parentElement.style.display='none'" class="text-red-700 hover:text-red-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
@endif

<!-- CONTROL PANEL -->
<div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
    <!-- Search & Filter Area (Sama seperti sebelumnya) -->
    <form action="{{ route('incidents.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
    <!-- Search Bar -->
    <div class="relative w-full md:w-64">
        <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari insiden/pelapor..." class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-[#1B4D3E] focus:border-[#1B4D3E] w-full" onchange="this.form.submit()">
    </div>

    <!-- Tombol Filter -->
    <div class="relative">
        <button type="button" onclick="toggleDropdown('filterDropdown')" class="bg-gray-100 border border-gray-300 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            Filter Data
            @if(request()->anyFilled(['severity', 'status', 'start_date', 'end_date']))
                <span class="bg-[#1B4D3E] text-white text-xs rounded-full w-4 h-4 flex items-center justify-center leading-none">!</span>
            @endif
        </button>

        <!-- Dropdown Panel -->
        <div id="filterDropdown" class="hidden absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-100 p-5 z-20">
            <h4 class="text-xs font-bold text-gray-500 uppercase mb-4 tracking-wider">Filter Lanjutan</h4>
            <div class="space-y-4">
                <!-- Filter Severity -->
                <div>
                    <label class="text-xs text-gray-600 block mb-1 font-bold">Tingkat Keparahan (Severity)</label>
                    <select name="severity" class="w-full border border-gray-300 rounded-md text-sm py-2 px-3 focus:ring-[#1B4D3E]">
                        <option value="">Semua Severity</option>
                        <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="medium"   {{ request('severity') == 'medium'   ? 'selected' : '' }}>Medium</option>
                        <option value="low"      {{ request('severity') == 'low'      ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <!-- Filter Status -->
                <div>
                    <label class="text-xs text-gray-600 block mb-1 font-bold">Status Penyelesaian</label>
                    <select name="status" class="w-full border border-gray-300 rounded-md text-sm py-2 px-3 focus:ring-[#1B4D3E]">
                        <option value="">Semua Status</option>
                        <option value="insiden_baru"        {{ request('status') == 'insiden_baru'        ? 'selected' : '' }}>Insiden Baru</option>
                        <option value="butuh_tindak_lanjut" {{ request('status') == 'butuh_tindak_lanjut' ? 'selected' : '' }}>Butuh Tindak Lanjut</option>
                        <option value="menunggu_verifikasi" {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="selesai"             {{ request('status') == 'selesai'             ? 'selected' : '' }}>Selesai</option>
                        <option value="ditolak"             {{ request('status') == 'ditolak'             ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <!-- Filter Rentang Tanggal -->
                <div>
                    <label class="text-xs text-gray-600 block mb-1 font-bold">Rentang Waktu</label>
                    <div class="flex items-center gap-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border border-gray-300 rounded-md text-xs py-2 px-2 focus:ring-[#1B4D3E]">
                        <span class="text-gray-400 font-bold">-</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border border-gray-300 rounded-md text-xs py-2 px-2 focus:ring-[#1B4D3E]">
                    </div>
                </div>
                <!-- Tombol Aksi -->
                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3 items-center">
                    @if(request()->anyFilled(['search', 'severity', 'status', 'start_date', 'end_date']))
                        <a href="{{ route('incidents.index') }}" class="text-xs text-red-600 hover:text-red-800 font-bold transition">Reset Filter</a>
                    @endif
                    <button type="submit" class="bg-[#1B4D3E] hover:bg-[#13382D] text-white text-xs font-bold px-4 py-2 rounded transition">Terapkan</button>
                </div>
            </div>
        </div>
    </div>
</form>

    <div class="flex items-center gap-3 w-full md:w-auto justify-end">
        <a href="{{ route('incidents.export') }}" class="flex items-center bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Export CSV
        </a>
        @if(session('role') == 'operator')
        <button onclick="toggleModal('addIncidentModal')" class="flex items-center bg-[#1B4D3E] hover:bg-[#13382D] text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm whitespace-nowrap">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Lapor Insiden
        </button>
        @endif
    </div>
</div>

<!-- TABEL UTAMA -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="py-3 px-6 border-b">Tanggal</th>
                    <th class="py-3 px-6 border-b">Pelapor & Judul</th>
                    <th class="py-3 px-6 border-b text-center">Severity</th>
                    <th class="py-3 px-6 border-b text-center">Status</th>
                    <th class="py-3 px-6 border-b text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($logs as $log)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="py-4 px-6 text-gray-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                    <td class="py-4 px-6">
                        <p class="font-bold text-gray-800">{{ $log->incident_title }}</p>
                        <p class="text-xs text-gray-500 mt-1">Oleh: {{ $log->reporter_name }}</p>
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if(empty($log->severity_level))
                            <span class="text-gray-400 italic text-xs">Belum Ditentukan</span>
                        @elseif($log->severity_level == 'critical')
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold inline-block">CRITICAL</span>
                        @elseif($log->severity_level == 'medium')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-bold inline-block">MEDIUM</span>
                        @else
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold inline-block">LOW</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($log->status == 'insiden_baru')
                            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">INSIDEN BARU</span>
                        @elseif($log->status == 'butuh_tindak_lanjut')
                            <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">BUTUH TINDAK LANJUT</span>
                        @elseif($log->status == 'menunggu_verifikasi')
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">MENUNGGU VERIFIKASI</span>
                        @elseif($log->status == 'selesai')
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">SELESAI</span>
                        @elseif($log->status == 'ditolak')
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">DITOLAK</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center space-x-2">
                            
                            <!-- LOGIKA TOMBOL BERDASARKAN ROLE & STATUS -->
                            @if(session('role') == 'supervisor')
                            <!-- Tombol Delete (paling kiri) -->
                            <form action="{{ route('incidents.destroy', $log->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus data ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 p-1.5 rounded transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>

                            <!-- Tombol Set Severity: ikon tanda seru (kanan delete) -->
                            @if($log->status == 'insiden_baru')
                                <button onclick="openSeverityModal({{ $log->id }})" 
                                    title="Set Severity"
                                    class="w-7 h-7 flex items-center justify-center rounded-full bg-amber-100 hover:bg-amber-200 text-amber-600 font-black text-sm transition border border-amber-300">
                                    !
                                </button>
                            @endif

                            <!-- Tombol Verifikasi: ikon dokumen (kanan delete) -->
                            @if($log->status == 'menunggu_verifikasi')
                                <button onclick="openVerifyModal({{ $log->id }}, '{{ asset('storage/' . $log->resolution_photo) }}', '{{ addslashes($log->resolution_notes) }}')"
                                    title="Verifikasi Laporan"
                                    class="w-7 h-7 flex items-center justify-center rounded-full bg-blue-100 hover:bg-blue-200 text-blue-600 transition border border-blue-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </button>
                            @endif
                        @endif

                            @if(session('role') == 'operator')
                                @if($log->status == 'butuh_tindak_lanjut' || $log->status == 'ditolak')
                                    <button onclick="openResolveModal({{ $log->id }})" class="bg-orange-600 text-white text-xs px-3 py-1.5 rounded hover:bg-orange-700 transition font-bold">Proses & Upload</button>
                                @elseif($log->status == 'selesai')
                                    <span class="text-green-600 font-bold text-xs"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></span>
                                @else
                                    <span class="text-gray-400 text-xs italic">Menunggu</span>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-10 text-center text-gray-500">Tidak ada data insiden ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 bg-gray-50 border-t border-gray-100">{{ $logs->links('pagination::tailwind') }}</div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. MODAL TAMBAH INSIDEN (OPERATOR) -->
<div id="addIncidentModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex justify-center items-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-[#1B4D3E] px-6 py-4 flex justify-between items-center text-white font-bold">
            <h3>Lapor Insiden Baru</h3>
            <button onclick="toggleModal('addIncidentModal')" class="hover:text-gray-300">&times;</button>
        </div>
        <div class="p-6">
            <form action="{{ route('incidents.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Judul Insiden <span class="text-red-500">*</span></label>
                    <input type="text" name="incident_title" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-[#1B4D3E]" placeholder="Cth: Mesin boiler mati">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Detail <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-[#1B4D3E]" placeholder="Jelaskan kronologi..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('addIncidentModal')" class="px-4 py-2 text-sm bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-[#1B4D3E] rounded-lg">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. MODAL SET SEVERITY (SUPERVISOR) -->
<div id="severityModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex justify-center items-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-[#1B4D3E] px-6 py-4 flex justify-between items-center text-white font-bold">
            <h3>Tentukan Tingkat Keparahan</h3>
            <button onclick="toggleModal('severityModal')" class="hover:text-gray-300">&times;</button>
        </div>
        <div class="p-6">
            <form id="severityForm" method="POST">
                @csrf @method('PUT')
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Severity Level <span class="text-red-500">*</span></label>
                    <select name="severity_level" required class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-[#1B4D3E]">
                        <option value="low">LOW (Normal)</option>
                        <option value="medium">MEDIUM (Peringatan)</option>
                        <option value="critical">CRITICAL (Darurat - Urgent Action!)</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('severityModal')" class="px-4 py-2 text-sm bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-[#1B4D3E] rounded-lg">Simpan & Lempar ke Operator</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 3. MODAL TINDAK LANJUT & UPLOAD FOTO (OPERATOR) -->
<div id="resolveModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex justify-center items-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-orange-600 px-6 py-4 flex justify-between items-center text-white font-bold">
            <h3>Tindak Lanjuti Insiden</h3>
            <button onclick="toggleModal('resolveModal')" class="hover:text-gray-300">&times;</button>
        </div>
        <div class="p-6">
            <form id="resolveForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Catatan Tindak Lanjut <span class="text-red-500">*</span></label>
                    <textarea name="resolution_notes" rows="3" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-orange-500" placeholder="Jelaskan apa yang sudah diperbaiki..."></textarea>
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Upload Foto Bukti <span class="text-red-500">*</span></label>
                    <input type="file" name="resolution_photo" accept="image/*" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG, Maks: 2MB.</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="toggleModal('resolveModal')" class="px-4 py-2 text-sm bg-gray-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-orange-600 rounded-lg font-bold">Kirim ke Supervisor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. MODAL VERIFIKASI (SUPERVISOR) -->
<div id="verifyModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex justify-center items-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-xl mx-4 overflow-hidden">
        <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white font-bold">
            <h3>Verifikasi Laporan Tindak Lanjut</h3>
            <button onclick="toggleModal('verifyModal')" class="hover:text-gray-300">&times;</button>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase">Foto Bukti:</label>
                <div class="mt-2 border rounded-lg overflow-hidden bg-gray-50 flex justify-center">
                    <img id="verifyPhoto" src="" alt="Bukti Foto" class="max-h-64 object-contain">
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Catatan Operator:</label>
                <div id="verifyNotes" class="bg-gray-100 p-3 rounded-lg text-sm text-gray-800 border border-gray-200"></div>
            </div>
            <form id="verifyForm" method="POST" class="border-t pt-4">
                @csrf @method('PUT')
                <div class="flex gap-4">
                    <button type="submit" name="verification_action" value="reject" class="flex-1 py-3 text-sm text-red-700 bg-red-100 hover:bg-red-200 rounded-lg font-bold border border-red-200 transition">Tolak (Ulangi)</button>
                    <button type="submit" name="verification_action" value="approve" class="flex-1 py-3 text-sm text-green-700 bg-green-100 hover:bg-green-200 rounded-lg font-bold border border-green-200 transition">Approve & Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- SCRIPT PENGENDALI MODAL -->
<script>
    function toggleModal(modalID) {
        document.getElementById(modalID).classList.toggle('hidden');
    }

    // Inject URL form untuk Supervisor Set Severity
    function openSeverityModal(id) {
        document.getElementById('severityForm').action = "{{ url('/incidents') }}/" + id + "/severity";
        toggleModal('severityModal');
    }

    // Inject URL form untuk Operator Upload Foto
    function openResolveModal(id) {
        document.getElementById('resolveForm').action = "{{ url('/incidents') }}/" + id + "/resolve";
        toggleModal('resolveModal');
    }

    // Inject Data ke Modal Verifikasi Supervisor
    function openVerifyModal(id, photoUrl, notes) {
        document.getElementById('verifyForm').action = "{{ url('/incidents') }}/" + id + "/verify";
        document.getElementById('verifyPhoto').src = photoUrl;
        document.getElementById('verifyNotes').innerText = notes;
        toggleModal('verifyModal');
    }
    function toggleDropdown(id) {
    document.getElementById(id).classList.toggle('hidden');
    }

    // Tutup dropdown jika klik di luar
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            const dd = document.getElementById('filterDropdown');
            if (dd) dd.classList.add('hidden');
        }
    });

</script>

@endsection