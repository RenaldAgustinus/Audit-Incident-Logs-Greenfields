@extends('layouts.app')

@section('page_title', 'User Profile')

@section('content')
<div class="bg-white p-8 rounded-lg shadow-sm border border-gray-100 max-w-2xl">
    <div class="flex items-center space-x-6 mb-8">
        <div class="w-24 h-24 bg-[#1B4D3E] rounded-full flex items-center justify-center text-white text-3xl font-bold shadow-md">
            {{ substr(session('full_name', 'U'), 0, 1) }}
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ session('full_name') }}</h2>
            <p class="text-gf-green font-semibold capitalize">{{ session('role') }} Greenfields</p>
        </div>
    </div>
    
    <div class="border-t border-gray-100 pt-6">
        <h3 class="font-bold text-gray-700 mb-4">Informasi Akun</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-500 uppercase">Status Akses</p>
                <p class="font-medium text-gray-800">Aktif (Verified)</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 uppercase">Departemen</p>
                <p class="font-medium text-gray-800">Operational Management</p>
            </div>
        </div>
    </div>
</div>
@endsection