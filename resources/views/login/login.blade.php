<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Greenfields Ops</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex h-screen bg-gray-50">

    <div class="hidden lg:flex lg:w-1/2 bg-cover bg-center relative" style="background-image: url('https://images.unsplash.com/photo-1596434455806-03f39db49a88?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80');">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
        <div class="absolute bottom-10 left-10 text-white">
            <h1 class="text-4xl font-bold mb-2">Precision in Nature.</h1>
            <p class="text-lg text-gray-200">Integrated operational management.</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="inline-block p-4 bg-[#1B4D3E] rounded-lg mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800">PT Greenfields</h2>
                <p class="text-sm font-semibold tracking-widest text-gray-500 uppercase mt-1">Operational Portal</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('authenticate') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <input type="text" name="username" placeholder="Username" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E]">
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-3 border border-gray-300 rounded focus:outline-none focus:border-[#1B4D3E] focus:ring-1 focus:ring-[#1B4D3E]">
                </div>
                <button type="submit" class="w-full bg-[#1B4D3E] hover:bg-[#13382D] text-white font-bold py-3 px-4 rounded transition">
                    LOGIN
                </button>
            </form>
        </div>
    </div>
</body>
</html>