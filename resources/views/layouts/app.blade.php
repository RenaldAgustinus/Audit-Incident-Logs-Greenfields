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

    <aside class="w-64 bg-[#2A3B32] text-white flex flex-col justify-between">
        <div>
            <div class="p-6 flex items-center space-x-3">
                <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span class="text-xl font-bold tracking-wider">Greenfields Ops</span>
            </div>
            
            <nav class="mt-4">
                <a href="{{ route('dashboard') }}" class="block py-3 px-6 hover:bg-gf-green bg-gf-green border-l-4 border-green-400 font-semibold transition">Dashboard</a>
                <a href="#" class="block py-3 px-6 hover:bg-gf-green border-l-4 border-transparent text-gray-300 transition">Incident Logs</a>
                <a href="#" class="block py-3 px-6 hover:bg-gf-green border-l-4 border-transparent text-gray-300 transition">Audit Trails</a>
            </nav>
        </div>

        <div class="p-6 border-t border-gray-700">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-2 text-gray-300 hover:text-white transition">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-700">@yield('page_title', 'Overview Dashboard')</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-500">Supervisor Admin</span>
                <div class="w-10 h-10 bg-gf-green rounded-full flex items-center justify-center text-white font-bold">SA</div>
            </div>
        </header>

        <div class="p-8">
            @yield('content')
        </div>
    </main>

</body>
</html>