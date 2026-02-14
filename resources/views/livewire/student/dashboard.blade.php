<div class="space-y-8 font-sans">
    
    <!-- 1. Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">
                {{ $greeting }}, <span class="text-brand-blue">{{ explode(' ', $student->user->name ?? 'Mahasiswa')[0] }}</span>! 👋
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm font-medium">
                {{ $student->nim ?? '-' }} &bull; {{ $student->studyProgram->name ?? 'Program Studi' }}
            </p>
        </div>
        
        <!-- Periode Aktif Badge -->
        @if($active_period)
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-4 py-2 rounded-xl flex items-center gap-3 shadow-sm">
            <div class="h-8 w-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Periode Aktif</p>
                <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $active_period->name }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- 2. Onboarding Alert (Jika Mahasiswa Baru) -->
    @if($show_onboarding)
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 skew-x-12 transform origin-bottom-left"></div>
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold mb-1">Selamat Datang di SIAKAD! 🚀</h3>
                <p class="text-blue-100 text-sm max-w-xl">
                    Lengkapi biodata dan profil Anda untuk memulai perkuliahan. Pastikan data diri sesuai dengan KTP/Ijazah.
                </p>
            </div>
            <button wire:click="dismissOnboarding" class="px-5 py-2.5 bg-white text-blue-700 font-bold rounded-xl text-sm shadow-md hover:bg-blue-50 transition">
                Saya Mengerti
            </button>
        </div>
    </div>
    @endif

    <!-- 3. Statistik Utama (Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- IPK -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between h-full">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Indeks Prestasi (IPK)</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($ipk, 2) }}</h3>
                </div>
                <div class="p-2 bg-yellow-100 text-yellow-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 w-full bg-slate-100 rounded-full h-1.5">
                <div class="bg-yellow-400 h-1.5 rounded-full" style="width: {{ ($ipk/4)*100 }}%"></div>
            </div>
        </div>

        <!-- SKS Semester Ini -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between h-full">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">SKS Semester Ini</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ $total_sks_semester }}</h3>
                </div>
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-xs font-medium text-slate-500">
                Max 24 SKS
            </div>
        </div>

        <!-- Total SKS Kumulatif -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between h-full">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total SKS Lulus</p>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ $total_sks_kumulatif }} <span class="text-sm font-medium text-slate-400 text-lg">/ {{ $target_sks }}</span></h3>
                </div>
                <div class="p-2 bg-purple-100 text-purple-600 rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 w-full bg-slate-100 rounded-full h-1.5">
                <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ min(($total_sks_kumulatif / $target_sks) * 100, 100) }}%"></div>
            </div>
        </div>

        <!-- Tagihan -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between h-full">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tagihan Belum Bayar</p>
                    <h3 class="text-2xl font-black {{ $tagihan_belum_bayar > 0 ? 'text-red-500' : 'text-green-500' }} mt-1">
                        Rp {{ number_format($tagihan_belum_bayar, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-2 {{ $tagihan_belum_bayar > 0 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} rounded-lg">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            @if($tagihan_belum_bayar > 0)
            <a href="#" class="mt-4 text-xs font-bold text-red-500 hover:underline flex items-center gap-1">
                Bayar Sekarang &rarr;
            </a>
            @else
            <p class="mt-4 text-xs font-bold text-green-500 flex items-center gap-1">
                Lunas & Aman &check;
            </p>
            @endif
        </div>
    </div>

    <!-- 4. Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Jadwal Hari Ini -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Jadwal Section -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="w-2 h-6 bg-brand-blue rounded-full"></span>
                        Jadwal Kuliah Hari Ini
                    </h3>
                    <span class="text-sm font-medium text-slate-500 bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>

                @if(count($jadwal_hari_ini) > 0)
                    <div class="space-y-4">
                        @foreach($jadwal_hari_ini as $jadwal)
                        <div class="flex group relative overflow-hidden rounded-2xl border border-slate-100 dark:border-slate-700 hover:border-blue-200 transition-all bg-slate-50/50 dark:bg-slate-700/30">
                            <!-- Time Bar -->
                            <div class="w-24 bg-white dark:bg-slate-800 flex flex-col justify-center items-center border-r border-slate-100 dark:border-slate-600 p-4">
                                <span class="text-lg font-black text-slate-800 dark:text-white">{{ \Carbon\Carbon::parse($jadwal->start_time)->format('H:i') }}</span>
                                <span class="text-xs text-slate-400 font-medium">s/d {{ \Carbon\Carbon::parse($jadwal->end_time)->format('H:i') }}</span>
                            </div>
                            
                            <!-- Class Info -->
                            <div class="flex-1 p-4 flex flex-col justify-center">
                                <h4 class="font-bold text-slate-800 dark:text-white text-lg group-hover:text-brand-blue transition-colors">
                                    {{ $jadwal->course_name }}
                                </h4>
                                <div class="flex items-center gap-3 mt-1 text-sm text-slate-500 dark:text-slate-400">
                                    <span class="flex items-center gap-1 bg-white dark:bg-slate-600 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-500">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                        Kelas {{ $jadwal->class_name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        {{ $jadwal->room }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action -->
                            <div class="w-16 bg-blue-50 dark:bg-slate-700 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="#" class="p-2 bg-brand-blue text-white rounded-lg shadow-lg hover:scale-110 transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-slate-200 rounded-2xl">
                        <div class="bg-slate-50 p-4 rounded-full mb-3">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h4 class="text-slate-900 font-bold">Tidak ada jadwal kuliah</h4>
                        <p class="text-slate-500 text-sm">Hari ini kamu bebas! Manfaatkan untuk istirahat atau belajar mandiri.</p>
                    </div>
                @endif
            </div>

            <!-- Pengumuman Section -->
            @if(count($announcements) > 0)
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                    <span class="w-2 h-6 bg-orange-500 rounded-full"></span>
                    Pengumuman Terbaru
                </h3>
                <div class="space-y-4">
                    @foreach($announcements as $info)
                    <div class="p-4 rounded-xl bg-orange-50 dark:bg-slate-700/50 border border-orange-100 dark:border-slate-600">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm">{{ $info->title }}</h4>
                            <span class="text-[10px] text-slate-400">{{ $info->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2">{{ $info->content }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <!-- Right Column: Grafik & Quick Links -->
        <div class="space-y-6">
            
            <!-- Chart Section -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">Riwayat SKS</h3>
                <div class="relative h-64">
                    <canvas id="sksChart"></canvas>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-xs text-slate-400">Total SKS yang diambil per semester</p>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">Akses Cepat</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="#" class="p-4 bg-blue-50 dark:bg-slate-700 rounded-xl text-center hover:bg-blue-100 transition group">
                        <span class="text-2xl mb-2 block group-hover:scale-110 transition-transform">📄</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-white">KRS Online</span>
                    </a>
                    <a href="#" class="p-4 bg-purple-50 dark:bg-slate-700 rounded-xl text-center hover:bg-purple-100 transition group">
                        <span class="text-2xl mb-2 block group-hover:scale-110 transition-transform">🎓</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-white">Transkrip</span>
                    </a>
                    <a href="#" class="p-4 bg-green-50 dark:bg-slate-700 rounded-xl text-center hover:bg-green-100 transition group">
                        <span class="text-2xl mb-2 block group-hover:scale-110 transition-transform">💰</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-white">Keuangan</span>
                    </a>
                    <a href="#" class="p-4 bg-orange-50 dark:bg-slate-700 rounded-xl text-center hover:bg-orange-100 transition group">
                        <span class="text-2xl mb-2 block group-hover:scale-110 transition-transform">📅</span>
                        <span class="text-xs font-bold text-slate-700 dark:text-white">Kalender</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- ChartJS Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('sksChart');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($sks_history_labels),
                    datasets: [{
                        label: 'SKS Diambil',
                        data: @json($sks_history_values),
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        borderRadius: 4,
                        barThickness: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
</div>