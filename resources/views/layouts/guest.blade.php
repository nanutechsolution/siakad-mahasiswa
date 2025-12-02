<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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

<body class="h-full font-sans antialiased text-slate-600 dark:text-slate-300">

    <div class="min-h-screen flex">

        <!-- BAGIAN KIRI: BRANDING & ART (Hidden on Mobile) -->
        <div
            class="hidden lg:flex w-1/2 bg-[#0F172A] relative overflow-hidden flex-col justify-between p-12 text-white">
            <!-- Background Elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-brand-blue/50 blur-[100px]">
            </div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-brand-gold/20 blur-[80px]">
            </div>
            <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20"></div>

            <!-- Top Brand -->
            <div class="relative z-10 flex items-center gap-3">
                <img src="{{ asset('logo.png') }}" class="h-10 w-auto drop-shadow-lg" alt="Logo">
                <div>
                    <h1 class="font-extrabold text-xl tracking-tight leading-none">UNMARIS</h1>
                    <p class="text-[10px] font-bold text-brand-gold tracking-[0.3em] uppercase mt-0.5">Sistem Akademik
                    </p>
                </div>
            </div>

            <!-- Middle Content -->
            <div class="relative z-10 max-w-md">
                <h2 class="text-4xl font-bold mb-4 leading-tight">Kelola Aktivitas Akademik dengan Mudah.</h2>
                <p class="text-slate-400 leading-relaxed">Platform terintegrasi untuk Dosen, Mahasiswa, dan Staff
                    Universitas Stella Maris Sumba.</p>
            </div>

            <!-- Footer -->
            <div class="relative z-10 text-xs text-slate-500">
                &copy; {{ date('Y') }} UNMARIS. All rights reserved.
            </div>
        </div>

        <!-- BAGIAN KANAN: FORM AREA -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 bg-white dark:bg-[#020617] relative">
            <!-- Mobile Background Blur (Only visible on mobile) -->
            <div class="absolute lg:hidden top-0 right-0 w-64 h-64 bg-brand-blue/10 blur-[80px]"></div>

            <div class="w-full max-w-sm">
                <!-- Mobile Logo (Only visible on mobile) -->
                <div class="lg:hidden flex justify-center mb-8">
                    <img src="{{ asset('logo.png') }}" class="h-12 w-auto" alt="Logo">
                </div>

                <!-- Slot Form -->
                {{ $slot }}
            </div>
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
            <svg x-show="type === 'success'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <!-- Icon Error -->
            <svg x-show="type === 'error'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>

            <span x-text="message"></span>
        </div>
    </div>
</body>

</html>
