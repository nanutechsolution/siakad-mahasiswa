<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Mahasiswa - UNMARIS</title>

    <!-- Font: Plus Jakarta Sans / Inter (Modern Geometric) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Hilangkan scrollbar default tapi tetap bisa scroll */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 20px;
        }
    </style>

    <!-- SCRIPT ANTI-FLICKER & LIVEWIRE HANDLER (FIXED) -->
    <script>
        // Fungsi untuk set tema
        function applyTheme() {
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        // 1. Jalankan saat halaman pertama kali dimuat (Hard Refresh)
        applyTheme();

        // 2. Jalankan setiap kali navigasi Livewire selesai (Soft Refresh/SPA)
        // Ini kuncinya agar tidak kembali ke Light Mode saat ganti menu
        document.addEventListener('livewire:navigated', () => {
            applyTheme();
        });
    </script>
    {{--  --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-[#F8FAFC] dark:bg-[#020617] text-slate-600 dark:text-slate-300"
    x-data="{
        sidebarOpen: false,
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
    }" x-init="$watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark'));">

    <div class="flex h-screen overflow-hidden">

        <!-- Mobile Backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/80 z-40 lg:hidden glass backdrop-blur-md"></div>

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col transition-transform duration-300 lg:relative lg:translate-x-0 flex-shrink-0
                   bg-slate-900 text-white shadow-2xl overflow-hidden border-r border-white/5">

            <!-- Background Art -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute -top-[10%] -right-[20%] w-80 h-80 rounded-full bg-brand-blue/40 blur-[80px]"></div>
                <div class="absolute bottom-[10%] -left-[10%] w-60 h-60 rounded-full bg-brand-gold/10 blur-[60px]">
                </div>
            </div>

            <!-- LOGO AREA -->
            <div class="flex h-24 shrink-0 items-center px-6 relative z-10">
                <div class="flex items-center gap-3 w-full group cursor-pointer">
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-brand-gold blur-lg opacity-20 group-hover:opacity-40 transition-opacity">
                        </div>
                        <img src="{{ asset('logo.png') }}" alt="Logo"
                            class="h-10 w-auto relative drop-shadow-xl transform group-hover:scale-110 transition-transform duration-300">
                    </div>
                    <div class="flex flex-col">
                        <h1
                            class="font-extrabold text-xl tracking-tight text-white leading-none group-hover:text-brand-gold transition-colors">
                            UNMARIS
                        </h1>
                        <span
                            class="text-[10px] font-bold text-slate-400 tracking-[0.3em] mt-1 group-hover:tracking-[0.4em] transition-all">
                            STUDENT
                        </span>
                    </div>
                </div>
            </div>

            <!-- MENU NAVIGATION -->
            <nav class="flex-1 overflow-y-auto px-4 py-4 custom-scrollbar relative z-10 space-y-8">

                <!-- Section: MAIN -->
                <div>
                    <p class="px-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-3">
                        Main Menu
                    </p>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('student.dashboard') }}" wire:navigate
                                class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 ease-out
                                      {{ request()->routeIs('student.dashboard')
                                          ? 'bg-brand-blue text-white shadow-[0_0_20px_rgba(26,35,126,0.4)] ring-1 ring-white/10 translate-x-1'
                                          : 'text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1' }}">

                                <div
                                    class="{{ request()->routeIs('student.dashboard') ? 'text-brand-gold' : 'text-slate-500 group-hover:text-brand-gold' }} transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" />
                                    </svg>
                                </div>
                                <span class="font-bold text-sm tracking-wide">Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Section: AKADEMIK -->
                <div>
                    <p class="px-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-3">
                        Akademik
                    </p>
                    <ul class="space-y-2">
                        <!-- KRS Online -->
                        <li>
                            <a href="{{ route('student.krs') }}" wire:navigate
                                class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 ease-out
                                      {{ request()->routeIs('student.krs')
                                          ? 'bg-brand-blue text-white shadow-[0_0_20px_rgba(26,35,126,0.4)] ring-1 ring-white/10 translate-x-1'
                                          : 'text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1' }}">

                                <div
                                    class="{{ request()->routeIs('student.krs') ? 'text-brand-gold' : 'text-slate-500 group-hover:text-brand-gold' }} transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                                <span class="font-bold text-sm tracking-wide">KRS Online</span>
                            </a>
                        </li>

                        <!-- Kartu Hasil Studi -->
                        <li>
                            <a href="{{ route('student.khs.index') }}" wire:navigate
                                class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 ease-out text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1">
                                <div class="text-slate-500 group-hover:text-brand-gold transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <span class="font-bold text-sm tracking-wide">Kartu Hasil Studi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.transcript') }}" wire:navigate
                                class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-300 ease-out
                                      {{ request()->routeIs('student.transcript')
                                          ? 'bg-brand-blue text-white shadow-[0_0_20px_rgba(26,35,126,0.4)] ring-1 ring-white/10 translate-x-1'
                                          : 'text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1' }}">
                                <div
                                    class="{{ request()->routeIs('student.transcript') ? 'text-brand-gold' : 'text-slate-500 group-hover:text-brand-gold' }} transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <span class="font-bold text-sm tracking-wide">Transkrip Nilai</span>
                            </a>
                        </li>

                        <x-admin-link href="{{ route('student.bills') }}" :active="request()->routeIs('student.bills')"
                            label="Tagihan & Pembayaran">
                        </x-admin-link>
                        <x-admin-link href="{{ route('student.edom.list') }}" :active="request()->routeIs('student.edom*')"
                            label="Evaluasi Dosen (EDOM)">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </x-admin-link>
                    </ul>
                </div>

                <div>
                    <p class="px-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-3">
                        Tugas Akhir
                    </p>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('student.thesis.proposal') }}" wire:navigate
                                class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 ease-out
                      {{ request()->routeIs('student.thesis.proposal')
                          ? 'bg-brand-blue text-white shadow-[0_0_20px_rgba(26,35,126,0.4)] ring-1 ring-white/10 translate-x-1'
                          : 'text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1' }}">

                                <div
                                    class="{{ request()->routeIs('student.thesis') ? 'text-brand-gold' : 'text-slate-500 group-hover:text-brand-gold' }} transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <span class="font-bold text-sm tracking-wide">Pengajuan Judul</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.thesis.log') }}" wire:navigate
                                class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 ease-out
              {{ request()->routeIs('student.thesis.log')
                  ? 'bg-brand-blue text-white shadow-[0_0_20px_rgba(26,35,126,0.4)] ring-1 ring-white/10 translate-x-1'
                  : 'text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1' }}">
                                <div
                                    class="{{ request()->routeIs('student.thesis.log') ? 'text-brand-gold' : 'text-slate-500 group-hover:text-brand-gold' }} transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                    </svg>
                                </div>
                                <span class="font-bold text-sm tracking-wide">Kartu Bimbingan</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Section: AKUN -->
                <div>
                    <p class="px-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-500 mb-3">
                        Personal
                    </p>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('student.profile') }}" wire:navigate
                                class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200 ease-out
                                      {{ request()->routeIs('student.profile')
                                          ? 'bg-brand-blue text-white shadow-[0_0_20px_rgba(26,35,126,0.4)] ring-1 ring-white/10 translate-x-1'
                                          : 'text-slate-400 hover:bg-white/5 hover:text-white hover:translate-x-1' }}">

                                <div
                                    class="{{ request()->routeIs('student.profile') ? 'text-brand-gold' : 'text-slate-500 group-hover:text-brand-gold' }} transition-colors">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span class="font-bold text-sm tracking-wide">Profil Saya</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </nav>

            <!-- FOOTER PROFILE CARD -->
            <div class="p-4 relative z-10">
                <div
                    class="bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-md rounded-3xl p-4 border border-white/10 shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <!-- Avatar -->
                        <div
                            class="h-10 w-10 shrink-0 rounded-full bg-gradient-to-br from-brand-gold to-orange-500 p-[2px] shadow-[0_0_15px_rgba(251,191,36,0.3)]">
                            <div
                                class="h-full w-full rounded-full bg-slate-900 flex items-center justify-center text-brand-gold font-extrabold text-sm">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-extrabold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] font-medium text-slate-400 truncate tracking-wide">
                                {{ Auth::user()->student->nim ?? 'NIM Tidak Ada' }}
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full group flex items-center justify-center gap-2 py-2.5 rounded-xl bg-red-500/10 hover:bg-red-500 text-xs font-bold text-red-400 hover:text-white transition-all duration-300 border border-red-500/20 hover:border-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.4)]">
                            <svg class="h-4 w-4 transition-transform group-hover:-translate-x-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            LOGOUT
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="flex flex-1 flex-col overflow-hidden">

            <!-- Topbar Floating -->
            <header
                class="h-20 flex items-center justify-between px-6 md:px-8 bg-[#F8FAFC] dark:bg-[#020617] transition-colors relative z-20 border-b border-slate-200/50 dark:border-slate-800">

                <!-- Mobile Toggle -->
                <button @click="sidebarOpen = true"
                    class="text-slate-500 lg:hidden hover:text-brand-blue transition-colors p-2 -ml-2">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Page Title / Breadcrumb -->
                <div class="hidden sm:block">
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $header ?? 'Portal Mahasiswa' }}
                    </h2>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">

                    <!-- Dark Mode Toggle (Pill Shape) -->
                    <button @click="toggleTheme()"
                        class="flex items-center gap-2 rounded-full bg-white dark:bg-slate-800 px-3 py-1.5 shadow-sm border border-slate-200 dark:border-slate-700 hover:border-brand-blue dark:hover:border-brand-blue transition-all">
                        <div class="text-slate-400 dark:text-brand-gold transition-colors">
                            <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </div>
                    </button>

                    <!-- Notification Bell -->
                    <button
                        class="relative rounded-full bg-white dark:bg-slate-800 p-2.5 shadow-sm border border-slate-200 dark:border-slate-700 hover:text-brand-blue dark:hover:text-white transition-all group">
                        <span
                            class="absolute top-2 right-2.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-slate-800 animate-pulse"></span>
                        <svg class="h-6 w-6 text-slate-400 group-hover:text-brand-blue dark:group-hover:text-white transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                </div>
            </header>

            <!-- CONTENT SCROLL AREA -->
            <main class="flex-1 overflow-y-auto overflow-x-hidden p-6 md:p-8 custom-scrollbar">
                {{ $slot }}
            </main>

        </div>

    </div>


    <!-- Letakkan di bagian bawah body -->
    <div x-data="{
        show: false,
        message: '',
        type: 'success',
        timeout: null
    }"
        x-on:notify.window="
        show = true; 
        message = $event.detail[0].message; 
        type = $event.detail[0].type;
        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(() => { show = false }, 3000);
     "
        class="fixed top-5 right-5 z-50 transition-all duration-300" style="display: none;" x-show="show"
        x-transition:enter="transform ease-out duration-300 transition"
        x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
        x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">

        <div class="rounded-lg shadow-lg p-4 text-white font-bold flex items-center gap-3"
            :class="{
                'bg-green-600': type === 'success',
                'bg-red-600': type === 'error',
                'bg-blue-600': type === 'info'
            }">
            <!-- Icon Success -->
            <svg x-show="type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <!-- Icon Error -->
            <svg x-show="type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                </path>
            </svg>

            <span x-text="message"></span>
        </div>
    </div>
</body>

</html>
