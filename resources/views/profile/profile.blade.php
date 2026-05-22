@extends('layouts.app')

@section('page_title', 'User Profile')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-sm border border-gray-100 w-full max-w-5xl mx-auto">
    <div class="flex items-center space-x-6 mb-8">
        <div class="w-24 h-24 bg-[#1B4D3E] rounded-full flex items-center justify-center text-white text-4xl font-bold shadow-md border-4 border-green-50">
            {{ substr(session('full_name', 'U'), 0, 1) }}
        </div>
        <div>
            <h2 class="text-3xl font-bold text-gray-800 mb-1">{{ session('full_name') }}</h2>
            <p class="text-gf-green font-semibold capitalize tracking-wide">{{ session('role') }} Greenfields</p>
        </div>
    </div>
    
    <div class="border-t border-gray-100 pt-6">
        <h3 class="font-bold text-gray-700 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            Informasi Akun
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-lg border border-gray-100">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Status Akses</p>
                <p class="font-medium text-green-600 flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Aktif (Verified)
                </p>
            </div>
            
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Departemen</p>
                <p class="font-medium text-gray-800">
                    @if(session('role') == 'supervisor')
                        Executive Management
                    @elseif(session('role') == 'operator')
                        Field Operations
                    @else
                        General Staff
                    @endif
                </p>
            </div>
            
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">ID Pengguna</p>
                <p class="font-mono text-gray-800 text-sm">GF-USR-{{ str_pad(session('user_id'), 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-wider font-bold mb-1">Tingkat Otorisasi</p>
                <p class="font-medium text-gray-800">
                    @if(session('role') == 'supervisor')
                        Level 2 (Read & Update)
                    @else
                        Level 1 (Read Only)
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection