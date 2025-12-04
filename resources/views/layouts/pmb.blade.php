<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PMB Online - UNMARIS</title>
    
    <!-- Font Modern -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-600">
    
    <!-- Navbar Sederhana -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" class="h-8 w-auto">
                    <div>
                        <h1 class="font-bold text-slate-900 text-lg leading-none">PMB UNMARIS</h1>
                        <p class="text-[10px] font-bold text-brand-gold tracking-widest uppercase">Penerimaan Mahasiswa Baru</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <span class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-red-600 hover:underline font-medium">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-brand-blue">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        {{ $slot }}
    </main>

    <footer class="text-center py-6 text-sm text-slate-400">
        &copy; {{ date('Y') }} Universitas Stella Maris Sumba.
    </footer>
</body>
</html>