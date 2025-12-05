<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Admin Console</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-300"
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

        <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/80 z-40 lg:hidden glass backdrop-blur-sm"></div>

        <!-- 2. SIDEBAR NAVIGATION -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col transition-transform duration-300 lg:static lg:translate-x-0 flex-shrink-0
                      /* Light: Putih, Dark: Navy Gelap (Bukan Hitam pekat, biar elegan) */
                      bg-white border-r border-slate-200 
                      dark:bg-[#0B1120] dark:border-slate-800">

            <!-- A. LOGO AREA (BRANDING KUAT DISINI) -->
            <div
                class="flex h-20 shrink-0 items-center justify-center border-b shadow-sm relative overflow-hidden
                        border-slate-200 bg-white 
                        dark:border-slate-800 dark:bg-[#0B1120] transition-colors">

                <!-- Hiasan Garis Kuning di Atas (Aksen) -->
                <div class="absolute top-0 w-full h-1 bg-brand-gold"></div>

                <div class="flex items-center gap-3">
                    <!-- LOGO IMAGE ASLI -->
                    <!-- Pastikan logo.png ada di folder public -->
                    <img src="{{ asset('logo.png') }}" alt="Logo UNMARIS" class="h-12 w-auto drop-shadow-md">

                    <div class="flex flex-col">
                        <h1
                            class="font-sans text-lg font-extrabold tracking-tight text-brand-blue dark:text-brand-gold leading-none">
                            UNMARIS
                        </h1>
                        <span
                            class="text-[10px] font-semibold text-slate-500 dark:text-slate-400 tracking-widest text-center">
                            SUMBA
                        </span>
                    </div>
                </div>
            </div>

            <!-- B. MENU AREA -->
            <nav class="flex-1 overflow-y-auto px-4 py-6 custom-scrollbar">

                <!-- GROUP: UTAMA -->
                <div class="mb-6">
                    <p
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-500">
                        Menu Utama
                    </p>
                    <div class="space-y-1">
                        <x-admin-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" label="Dashboard">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </x-admin-link>
                    </div>
                </div>

                <!-- GROUP: AKADEMIK -->
                <div class="mb-6">
                    <p
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-500">
                        Akademik
                    </p>
                    <div class="space-y-1">
                        <x-admin-link href="{{ route('admin.academic.courses') }}" :active="request()->routeIs('admin.academic.courses')"
                            label="Mata Kuliah">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.academic.classrooms') }}" :active="request()->routeIs('admin.academic.classrooms')"
                            label="Penjadwalan Kelas">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.master.periods') }}" :active="request()->routeIs('admin.master.periods')"
                            label="Periode / Semester">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.academic.krs-validation') }}" :active="request()->routeIs('admin.academic.krs-validation')"
                            label="Validasi KRS">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.academic.krs-management') }}" :active="request()->routeIs('admin.academic.krs-management')"
                            label="Input KRS Manual">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.academic.krs-generate') }}" :active="request()->routeIs('admin.academic.krs-generate')"
                            label="Generate Paket KRS">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.academic.advisor.plotting') }}" :active="request()->routeIs('admin.academic.advisor.plotting')" label="Plotting Dosen Wali">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
</x-admin-link>

                        <x-admin-link href="{{ route('admin.academic.theses') }}" :active="request()->routeIs('admin.academic.theses')"
                            label="Skripsi & TA">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </x-admin-link>
                    </div>


                </div>

                <!-- GROUP: PENGGUNA -->
                <div class="mb-6">
                    <p
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-500">
                        Master Data
                    </p>
                    <div class="space-y-1">
                        <x-admin-link href="{{ route('admin.master.faculties') }}" :active="request()->routeIs('admin.master.faculties')"
                            label="Data Fakultas">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.master.prodi') }}" :active="request()->routeIs('admin.master.prodi')"
                            label="Program Studi">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.master.lecturers') }}" :active="request()->routeIs('admin.master.lecturers')"
                            label="Data Dosen">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.master.students') }}" :active="request()->routeIs('admin.master.students')"
                            label="Data Mahasiswa">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </x-admin-link>
                    </div>
                </div>
                <div class="mb-6">
                    <p
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-500">
                        Keuangan
                    </p>
                    <div class="space-y-1">
                        <x-admin-link href="{{ route('admin.finance.billings') }}" :active="request()->routeIs('admin.finance.billings')"
                            label="Kelola Tagihan">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-admin-link>

                        <x-admin-link href="{{ route('admin.finance.payments') }}" :active="request()->routeIs('admin.finance.payments')"
                            label="Verifikasi Pembayaran">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </x-admin-link>
                    </div>
                </div>

                <div class="mb-6">
                    <p
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-500">
                        Penjaminan Mutu
                    </p>
                    <div class="space-y-1">
                        <x-admin-link href="{{ route('admin.lpm.edom.master') }}" :active="request()->routeIs('admin.lpm.edom.master')"
                            label="Instrumen EDOM">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.lpm.edom.result') }}" :active="request()->routeIs('admin.lpm.edom.result')"
                            label="Hasil Evaluasi (Rapor)">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </x-admin-link>
                    </div>
                </div>

                <div class="mb-6">
                    <p
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-500">
                        Penerimaan Maba
                    </p>
                    <div class="space-y-1">
                        <x-admin-link href="{{ route('admin.pmb.dashboard') }}" :active="request()->routeIs('admin.pmb.dashboard')"
                            label="Dashboard PMB">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.pmb.registrants') }}" :active="request()->routeIs('admin.pmb.registrants')"
                            label="Seleksi Pendaftar">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.pmb.waves') }}" :active="request()->routeIs('admin.pmb.waves')"
                            label="Pengaturan Gelombang">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </x-admin-link>
                    </div>
                </div>

                <div class="mb-6">
                    <p
                        class="mb-2 px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-500">
                        Konfigurasi
                    </p>
                    <div class="space-y-1">
                        <x-admin-link href="{{ route('admin.settings') }}" :active="request()->routeIs('admin.settings')"
                            label="Pengaturan Sistem">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </x-admin-link>
                        <x-admin-link href="{{ route('admin.settings.nim') }}" :active="request()->routeIs('admin.settings.nim')" label="Format NIM">
    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
</x-admin-link>
                    </div>
                </div>
            </nav>

            <!-- C. PROFILE BOTTOM -->
            <div
                class="border-t p-4 transition-colors
                        border-slate-200 bg-slate-50
                        dark:border-slate-800 dark:bg-[#0B1120]">

                <div
                    class="flex items-center gap-3 rounded-xl p-2.5 ring-1 ring-inset transition-colors
                            bg-white ring-slate-200 
                            dark:bg-slate-900 dark:ring-slate-800">

                    <!-- Avatar dengan Warna Kuning Emas Logo -->
                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-gold font-bold text-brand-blue shadow-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>

                    <div class="flex min-w-0 flex-1 flex-col">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white">
                            {{ Auth::user()->name }}</p>
                        <p class="truncate text-[10px] font-medium text-slate-500 dark:text-slate-400">Administrator
                        </p>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="group flex h-8 w-8 items-center justify-center rounded-lg transition-all
                                                     text-slate-400 hover:bg-red-50 hover:text-red-600
                                                     dark:text-slate-500 dark:hover:bg-red-500/10 dark:hover:text-red-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex flex-1 flex-col overflow-hidden">

            <header
                class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-6 dark:border-slate-700 dark:bg-slate-800 transition-colors">
                <button @click="sidebarOpen = true" class="text-slate-500 lg:hidden dark:text-slate-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <h2 class="hidden text-xl font-semibold text-slate-800 dark:text-white sm:block">
                    {{ $header ?? 'Dashboard' }}
                </h2>

                <div class="flex items-center gap-4">
                    <button @click="toggleTheme()"
                        class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-indigo-500 dark:hover:bg-slate-700 transition-all">
                        <svg x-show="darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg x-show="!darkMode" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                    <button class="relative text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <span
                            class="absolute right-0 top-0 h-2 w-2 rounded-full border border-white bg-red-500 dark:border-slate-800"></span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto overflow-x-hidden p-6">
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
