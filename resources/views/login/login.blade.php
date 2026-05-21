<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Greenfields Ops</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex h-screen bg-gray-50">

    <div class="hidden lg:flex lg:w-2/3 bg-cover bg-center relative" style="background-image: url('{{ asset('images/bg-login.jpg') }}');">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
        <div class="absolute bottom-12 left-12 text-white">
            <h1 class="text-5xl font-bold mb-3">Precision in Nature.</h1>
            <p class="text-xl text-gray-200">Integrated operational management.</p>
        </div>
    </div>

    <div class="w-full lg:w-1/3 flex items-center justify-center p-8 bg-white shadow-[-10px_0_15px_-3px_rgba(0,0,0,0.1)] z-10">
        <div class="w-full max-w-sm">
            
            <div class="text-center mb-10">
                <img src="{{ asset('images/logo.png') }}" alt="PT Greenfields" class="h-40 mx-auto object-contain">
            </div>

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('authenticate') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <input type="text" name="username" placeholder="Username" required class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:border-green-600 focus:ring-1 focus:ring-green-600 transition">
                </div>
                <div>
                    <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:border-green-600 focus:ring-1 focus:ring-green-600 transition">
                </div>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition mt-4 shadow-md tracking-wider">
                    LOGIN
                </button>
            </form>
            
            <p class="text-center text-xs text-gray-400 mt-10">
                &copy; {{ date('Y') }} PT Greenfields. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>