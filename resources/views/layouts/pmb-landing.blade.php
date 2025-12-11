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

    <!-- Script Anti-Flicker & Livewire Handler -->
    <script>
        function applyTheme() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
        applyTheme();
        document.addEventListener('livewire:navigated', () => {
            applyTheme();
        });
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-white dark:bg-slate-900 min-h-screen font-sans text-slate-600 dark:text-slate-300 selection:bg-brand-gold selection:text-white flex flex-col"
    x-data="{
        mobileMenuOpen: false,
        darkMode: localStorage.getItem('theme') === 'dark',
        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }" x-init="$watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark'));
    if (darkMode) document.documentElement.classList.add('dark');">

    <!-- NAVBAR -->
    <nav
        class="fixed top-0 w-full z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" class="h-10 w-auto" alt="Logo UNMARIS">
                    <div>
                        <h1 class="font-black text-slate-900 dark:text-white text-lg leading-none tracking-tight">PMB
                            UNMARIS</h1>
                        <p class="text-[10px] font-bold text-brand-gold tracking-widest uppercase mt-0.5">Penerimaan
                            Mahasiswa Baru</p>
                    </div>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}"
                        class="text-sm font-bold text-slate-600 hover:text-brand-blue dark:text-slate-300 dark:hover:text-white transition-colors">Beranda</a>
                    <a href="#prodi"
                        class="text-sm font-bold text-slate-600 hover:text-brand-blue dark:text-slate-300 dark:hover:text-white transition-colors">Program
                        Studi</a>
                    <a href="{{ route('pmb.info') }}"
                        class="text-sm font-bold text-slate-600 hover:text-brand-blue dark:text-slate-300 dark:hover:text-white transition-colors">Informasi
                        & Jadwal</a>
                </div>

                <!-- Right Actions (Desktop) -->
                <div class="hidden md:flex items-center gap-4">
                    <!-- Dark Mode Toggle -->
                    <button @click="toggleTheme()"
                        class="p-2 rounded-full text-slate-400 hover:bg-slate-100 hover:text-brand-blue dark:hover:bg-slate-800 transition-all">
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="px-5 py-2.5 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-white rounded-xl text-sm font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                            Dashboard Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="text-sm font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">Masuk</a>
                        <a href="{{ route('register') }}"
                            class="px-5 py-2.5 bg-brand-blue text-white rounded-xl text-sm font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 transform hover:-translate-y-0.5">
                            Daftar Sekarang
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center gap-4 md:hidden">
                    <button @click="toggleTheme()" class="text-slate-400">
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileMenuOpen" x-transition.opacity @click.outside="mobileMenuOpen = false"
            class="md:hidden absolute top-20 left-0 w-full bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 shadow-xl p-4 flex flex-col gap-4">
            <a href="{{ route('home') }}"
                class="block py-2 text-sm font-bold text-slate-600 dark:text-slate-300">Beranda</a>
            <a href="#prodi" class="block py-2 text-sm font-bold text-slate-600 dark:text-slate-300">Program Studi</a>
            <a href="{{ route('pmb.info') }}"
                class="block py-2 text-sm font-bold text-slate-600 dark:text-slate-300">Informasi & Jadwal</a>
            <hr class="border-slate-100 dark:border-slate-800">
            @auth
                <a href="{{ route('dashboard') }}"
                    class="block text-center py-3 bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-white rounded-xl text-sm font-bold">Dashboard
                    Saya</a>
            @else
                <a href="{{ route('login') }}"
                    class="block text-center py-3 text-sm font-bold text-slate-600 dark:text-slate-300">Masuk</a>
                <a href="{{ route('register') }}"
                    class="block text-center py-3 bg-brand-blue text-white rounded-xl text-sm font-bold shadow-lg">Daftar
                    Sekarang</a>
            @endauth
        </div>
    </nav>

    <!-- CONTENT SLOT -->
    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-white py-12 border-t border-slate-800 mt-auto relative overflow-hidden">
        <!-- Background Elements -->
        <div
            class="absolute top-0 left-0 w-full h-full bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-5">
        </div>
        <div class="absolute -top-24 -left-24 w-64 h-64 bg-brand-blue/20 rounded-full blur-[80px]"></div>

        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <img src="{{ asset('logo.png') }}" class="h-12 w-auto mx-auto mb-6 opacity-90 drop-shadow-lg"
                alt="Logo Footer">
            <p class="text-slate-400 text-sm max-w-md mx-auto mb-8 leading-relaxed">
                Universitas Stella Maris Sumba berkomitmen mencetak lulusan berkualitas, berkarakter, dan siap bersaing
                di era global.
            </p>
            <div class="flex flex-wrap justify-center gap-6 text-xs font-bold text-slate-500 uppercase tracking-widest">
                <a href="#" class="hover:text-white transition-colors">Panduan Pendaftaran</a>
                <a href="#" class="hover:text-white transition-colors">Beasiswa</a>
                <a href="#" class="hover:text-white transition-colors">Fakultas</a>
                <a href="#" class="hover:text-white transition-colors">Kontak Kami</a>
            </div>
            <div class="mt-8 pt-8 border-t border-white/10 text-xs text-slate-600">
                &copy; {{ date('Y') }} Universitas Stella Maris Sumba. All rights reserved.
            </div>
        </div>
    </footer>

</body>

</html>