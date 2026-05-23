<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Greenfields - Operational Portal</title>
    

    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        /* Warna Corporate Greenfields */
        .bg-gf-green { background-color: #1B4D3E; }
        .text-gf-green { color: #1B4D3E; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden text-gray-800">

    <aside class="w-64 bg-[#2A3B32] text-white flex flex-col justify-between h-screen shrink-0">
        <div>
            <div class="p-6 flex justify-center items-center mb-4">
                <img src="{{ asset('images/logoputih.png') }}" alt="PT Greenfields" class="w-44 h-auto object-contain hover:opacity-80 transition cursor-pointer">
            </div>
            
            <nav class="flex flex-col space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center py-3 px-6 {{ request()->routeIs('dashboard') ? 'bg-[#1B4D3E] border-l-4 border-green-400 text-white font-semibold' : 'hover:bg-[#1B4D3E] border-l-4 border-transparent text-gray-300 hover:text-white transition' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>
                
                <a href="{{ route('incidents.index') }}" class="flex items-center py-3 px-6 {{ request()->routeIs('incidents.*') ? 'bg-[#1B4D3E] border-l-4 border-green-400 text-white font-semibold' : 'hover:bg-[#1B4D3E] border-l-4 border-transparent text-gray-300 hover:text-white transition' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Incident Logs
                </a>
                
                <a href="{{ route('audits.index') }}" class="flex items-center py-3 px-6 {{ request()->routeIs('audits.*') ? 'bg-[#1B4D3E] border-l-4 border-green-400 text-white font-semibold' : 'hover:bg-[#1B4D3E] border-l-4 border-transparent text-gray-300 hover:text-white transition' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Audit Trails
                </a>
            </nav>
        </div>

        <div class="p-6 border-t border-gray-700 mt-auto">
            <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem operasional?');">
                @csrf
                <button type="submit" class="flex items-center w-full px-2 py-2 text-gray-400 hover:text-red-400 hover:bg-[#1a251f] rounded transition">
                    <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center shrink-0">
        <h1 class="text-2xl font-bold text-gray-700">@yield('page_title', 'Attention Logic Dashboard')</h1>
        
        <a href="{{ route('profile') }}" class="flex items-center space-x-3 hover:bg-gray-50 p-2 rounded-lg transition border border-transparent hover:border-gray-200">
            <div class="text-right hidden md:block">
                <p class="text-sm font-bold text-gray-700">{{ session('full_name') }}</p>
                <p class="text-xs text-gray-500 capitalize">{{ session('role') }}</p>
            </div>
            <div class="w-10 h-10 bg-[#1B4D3E] rounded-full flex items-center justify-center text-white font-bold">
                {{ substr(session('full_name', 'U'), 0, 2) }}
            </div>
        </a>
    </header>

        <div class="p-8">
            @yield('content')
        </div>
    </main>

</body>
</html>