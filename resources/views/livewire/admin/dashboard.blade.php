<div class="space-y-8 font-sans">
    <x-slot name="header">Dashboard Eksekutif</x-slot>

    <!-- 1. WELCOME BANNER -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-8 text-white shadow-xl">
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-brand-blue to-transparent opacity-40"></div>
        <div class="absolute -right-10 -bottom-20 h-64 w-64 rounded-full bg-brand-gold/20 blur-3xl"></div>
        
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-blue-200 font-bold tracking-wider text-xs uppercase mb-1">
                    Semester Aktif: {{ $semester_aktif->name ?? 'Belum Diatur' }}
                </p>
                <h2 class="text-3xl font-black">Halo, Administrator! 👋</h2>
                <p class="text-slate-300 mt-2 max-w-xl">
                    Berikut adalah ringkasan data akademik dan keuangan Universitas Stella Maris Sumba secara real-time.
                </p>
            </div>
            <!-- Quick Action -->
            <div class="hidden md:flex gap-3">
                <a href="{{ route('admin.pmb.registrants') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 rounded-xl font-bold text-sm transition backdrop-blur-md border border-white/10">
                    Cek PMB Baru
                </a>
                <a href="{{ route('admin.finance.billings') }}" class="px-5 py-2.5 bg-brand-gold text-brand-blue rounded-xl font-bold text-sm hover:bg-yellow-400 transition shadow-lg shadow-yellow-500/20">
                    Kelola Keuangan
                </a>
            </div>
        </div>
    </div>

    <!-- 2. STATS OVERVIEW -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Mahasiswa -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-brand-blue" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Mahasiswa Aktif</p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-2">{{ $total_mhs }}</h3>
            <p class="text-sm text-green-600 font-bold mt-2 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                Terdaftar
            </p>
        </div>

        <!-- Dosen -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-purple-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Dosen</p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-2">{{ $total_dosen }}</h3>
            <p class="text-sm text-slate-500 font-medium mt-2">Pengajar Tetap & LB</p>
        </div>

        <!-- Prodi -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M4 10v7h3v-7H4zm6 0v7h3v-7h-3zM2 22h19v-3H2v3zm14-12v7h3v-7h-3zm-4.5-9L2 6v2h19V6l-9.5-5z"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Program Studi</p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-2">{{ $total_prodi }}</h3>
            <p class="text-sm text-slate-500 font-medium mt-2">Fakultas & Jurusan</p>
        </div>
    </div>

    <!-- 3. CHARTS ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Chart: Mahasiswa Per Prodi -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h4 class="font-bold text-lg text-slate-800 dark:text-white mb-6">Sebaran Mahasiswa per Prodi</h4>
            <div id="prodiChart" class="h-80"></div>
        </div>

        <!-- Chart: Keuangan -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col">
            <h4 class="font-bold text-lg text-slate-800 dark:text-white mb-2">Status SPP Semester Ini</h4>
            <p class="text-xs text-slate-500 mb-6">Perbandingan mahasiswa Lunas vs Belum.</p>
            
            <div class="flex-1 flex items-center justify-center">
                <div id="financeChart" class="w-full"></div>
            </div>

            <!-- Custom Legend -->
            <div class="mt-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span> Lunas</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $finance_stats[0] }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span> Belum Lunas</span>
                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $finance_stats[1] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. AKTIVITAS TERBARU (Jika Ada) -->
    @if(count($activities) > 0)
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
            <h4 class="font-bold text-slate-800 dark:text-white">Aktivitas Sistem Terbaru</h4>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($activities as $log)
            <div class="px-6 py-4 flex items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm dark:bg-indigo-900/30">
                    {{ substr($log->user->name ?? 'S', 0, 1) }}
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-800 dark:text-white">{{ $log->user->name ?? 'System' }}</p>
                    <p class="text-xs text-slate-500">{{ $log->description }}</p>
                </div>
                <span class="text-xs text-slate-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- Scripts Chart -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:navigated', () => {
        
        // 1. Chart Prodi (Bar)
        var optionsProdi = {
            series: [{
                name: 'Jumlah Mahasiswa',
                data: @json($chart_prodi_values)
            }],
            chart: {
                type: 'bar',
                height: 320,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false }
            },
            plotOptions: {
                bar: { borderRadius: 6, horizontal: true, barHeight: '60%' }
            },
            dataLabels: { enabled: false },
            colors: ['#1a237e'],
            xaxis: {
                categories: @json($chart_prodi_labels),
            },
            grid: { borderColor: '#f1f5f9' },
            tooltip: { theme: 'dark' }
        };
        var chart1 = new ApexCharts(document.querySelector("#prodiChart"), optionsProdi);
        chart1.render();

        // 2. Chart Keuangan (Donut)
        var optionsFinance = {
            series: @json($finance_stats), // [Paid, Unpaid]
            chart: {
                type: 'donut',
                height: 250,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
            },
            labels: ['Lunas', 'Belum Lunas'],
            colors: ['#10b981', '#ef4444'],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Tagihan',
                                fontSize: '12px',
                                fontWeight: 'bold',
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { show: false },
            stroke: { show: false },
            tooltip: { theme: 'dark' }
        };
        var chart2 = new ApexCharts(document.querySelector("#financeChart"), optionsFinance);
        chart2.render();
    });
</script>