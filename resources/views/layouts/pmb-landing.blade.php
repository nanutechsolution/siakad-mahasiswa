    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? 'PMB UNMARIS' }}</title>

        <!-- Font: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
            rel="stylesheet">

        <style>
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body
        class="bg-white dark:bg-slate-900 min-h-screen font-sans text-slate-600 dark:text-slate-300 selection:bg-brand-gold selection:text-white flex flex-col">

        <!-- NAVBAR -->
        <nav
            class="fixed top-0 w-full z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('logo.png') }}" class="h-8 w-auto" alt="Logo UNMARIS">
                        <div>
                            <h1 class="font-black text-slate-900 dark:text-white text-lg leading-none tracking-tight">
                                PMB UNMARIS</h1>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="text-sm font-bold text-brand-blue dark:text-blue-400 hover:underline">Dashboard
                                Saya</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">Masuk</a>
                            <a href="{{ route('register') }}"
                                class="px-4 py-2 bg-brand-blue text-white rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20">
                                Daftar Akun
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- CONTENT SLOT -->
        <main class="flex-grow">
            {{ $slot }}
        </main>

        <!-- FOOTER -->
        <footer class="bg-slate-900 text-white py-12 border-t border-slate-800 mt-auto">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <img src="{{ asset('logo.png') }}" class="h-12 w-auto mx-auto mb-6 opacity-80" alt="Logo Footer">
                <p class="text-slate-400 text-sm">&copy; {{ date('Y') }} Universitas Stella Maris Sumba. All rights
                    reserved.</p>
                <div class="mt-4 flex justify-center gap-4 text-xs text-slate-500">
                    <a href="#" class="hover:text-white transition-colors">Panduan Pendaftaran</a>
                    <span>&bull;</span>
                    <a href="#" class="hover:text-white transition-colors">Kontak Kami</a>
                    <span>&bull;</span>
                    <a href="#" class="hover:text-white transition-colors">Fakultas & Prodi</a>
                </div>
            </div>
        </footer>

    </body>

    </html>
