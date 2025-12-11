<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Dosen - UNMARIS</title>

    <!-- Font Modern -->
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
    class="font-sans antialiased bg-[#F8FAFC] dark:bg-[#020617] text-slate-600 dark:text-slate-300 transition-colors duration-300"
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
    }" x-init="$watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark'));
    if (darkMode) document.documentElement.classList.add('dark');">

    <div class="flex h-screen overflow-hidden">

        <!-- 1. MOBILE BACKDROP (Overlay Gelap saat menu buka di HP) -->
        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/80 z-40 lg:hidden glass backdrop-blur-sm"></div>

        <!-- 2. SIDEBAR (Gradient Theme) -->
        <!-- PERBAIKAN DI SINI: Ganti 'lg:static' jadi 'lg:relative' dan hapus 'relative' global -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col transition-transform duration-300 lg:relative lg:translate-x-0 flex-shrink-0
                   bg-gradient-to-b from-[#1a237e] to-[#0f172a] text-white shadow-2xl overflow-hidden border-r border-white/10">

            <!-- Background Decoration -->
            <div
                class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white/5 blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-brand-gold/10 blur-3xl pointer-events-none">
            </div>

            <!-- LOGO AREA -->
            <div class="flex h-24 shrink-0 items-center px-6 relative z-10">
                <div class="flex items-center gap-3 w-full">
                    <div class="p-2 bg-white/10 rounded-xl backdrop-blur-sm shadow-inner ring-1 ring-white/20">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="h-8 w-auto drop-shadow-md">
                    </div>
                    <div class="flex flex-col">
                        <h1 class="font-extrabold text-xl tracking-tight text-white leading-none">
                            UNMARIS
                        </h1>
                        <span class="text-[10px] font-bold text-brand-gold tracking-[0.3em] mt-1">
                            DOSEN
                        </span>
                    </div>
                </div>
            </div>

            <!-- MENU NAVIGATION -->
            <nav class="flex-1 overflow-y-auto px-4 py-4 custom-scrollbar relative z-10 space-y-8">

                <!-- Group: UTAMA -->
                <div>
                    <p class="px-4 text-[10px] font-extrabold uppercase tracking-widest text-blue-200/60 mb-3">
                        Menu Utama
                    </p>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('lecturer.dashboard') }}"
                                class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200
                                      {{ request()->routeIs('lecturer.dashboard')
                                          ? 'bg-brand-gold text-brand-blue font-bold shadow-[0_0_20px_rgba(251,191,36,0.3)]'
                                          : 'text-blue-100 hover:bg-white/10 hover:text-white hover:translate-x-1' }}">

                                <svg class="h-5 w-5 {{ request()->routeIs('lecturer.dashboard') ? 'text-brand-blue' : 'text-blue-300 group-hover:text-white' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                <span>Dashboard & Jadwal</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group: PERKULIAHAN -->
                <div>
                    <p class="px-4 text-[10px] font-extrabold uppercase tracking-widest text-blue-200/60 mb-3">
                        Akademik
                    </p>
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('lecturer.grading.index') }}"
                                class="group flex items-center gap-3 px-4 py-3 rounded-2xl transition-all duration-200
                                      {{ request()->routeIs('lecturer.grading*')
                                          ? 'bg-brand-gold text-brand-blue font-bold shadow-[0_0_20px_rgba(251,191,36,0.3)]'
                                          : 'text-blue-100 hover:bg-white/10 hover:text-white hover:translate-x-1' }}">

                                <svg class="h-5 w-5 {{ request()->routeIs('lecturer.grading*') ? 'text-brand-blue' : 'text-blue-300 group-hover:text-white' }}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Input Nilai</span>
                            </a>
                          
                        </li>
                        <x-admin-link href="{{ route('lecturer.krs.validation') }}" :active="request()->routeIs('lecturer.krs.validation')"
                            label="Validasi KRS">
                            <svg class="h-5 w-5 "
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-admin-link>

                        <x-admin-link href="{{ route('lecturer.edom.report') }}" :active="request()->routeIs('lecturer.edom.report')"
                            label="Rapor Kinerja (EDOM)">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </x-admin-link>

                        <x-admin-link href="{{ route('lecturer.thesis.index') }}" :active="request()->routeIs('lecturer.thesis*')"
                            label="Bimbingan Skripsi">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </x-admin-link>
                    </ul>
                </div>

            </nav>

            <!-- FOOTER PROFILE -->
            <div class="p-4 relative z-10">
                <div class="bg-black/20 backdrop-blur-md rounded-3xl p-4 border border-white/10 shadow-lg">
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
                            <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-blue-200 truncate font-medium">
                                {{ Auth::user()->lecturer->nidn ?? 'NIDN Tidak Ada' }}
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl bg-white/10 hover:bg-red-500/80 text-xs font-bold text-white transition-all border border-white/5 shadow-sm hover:shadow-red-500/20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
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

                <!-- HAMBURGER BUTTON (MOBILE ONLY) -->
                <!-- Ini yang membuat sidebar bisa dibuka di HP -->
                <button @click="sidebarOpen = true"
                    class="text-slate-500 lg:hidden hover:text-brand-blue transition-colors p-2 -ml-2">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Page Title -->
                <div class="hidden sm:block">
                    <h2 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight">
                        {{ $header ?? 'Portal Dosen' }}
                    </h2>
                </div>

                <!-- Right Actions -->
                <div class="flex items-center gap-4">

                    <!-- Dark Mode Toggle -->
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

                    <!-- Profile Bubble -->
                    <div
                        class="h-10 w-10 rounded-full bg-brand-blue flex items-center justify-center text-white font-bold shadow-lg shadow-blue-900/20 ring-2 ring-white dark:ring-slate-700">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
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
