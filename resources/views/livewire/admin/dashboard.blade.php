<div class="space-y-8 font-sans">
    <x-slot name="header">Dashboard Eksekutif</x-slot>

    <!-- 1. WELCOME BANNER -->
    <div class="relative overflow-hidden rounded-3xl bg-slate-900 p-8 text-white shadow-xl">
        <!-- Background Ornaments -->
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-brand-blue to-transparent opacity-40"></div>
        <div class="absolute -right-10 -bottom-20 h-64 w-64 rounded-full bg-brand-gold/20 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div>
                <h2 class="text-3xl font-black">Dashboard & Analisa</h2>
                <p class="text-slate-300 mt-1">Ringkasan data akademik dan keuangan.</p>
            </div>
             <!-- Quick Action -->
             <div class="flex gap-3">
                <a href="{{ route('admin.finance.billings') }}" class="px-5 py-2.5 bg-brand-gold text-brand-blue rounded-xl font-bold text-sm hover:bg-yellow-400 transition shadow-lg">
                    Keuangan
                </a>
            </div>
        </div>
    </div>

    <!-- 2. FILTER SECTION -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="flex items-center gap-2 text-slate-500">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
            <span class="font-bold text-sm uppercase tracking-wide">Filter Data:</span>
        </div>
        <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
            <!-- Filter Periode -->
            <select wire:model.live="selected_period_id" class="px-4 py-2 rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm focus:ring-brand-blue focus:border-brand-blue">
                @foreach($periods as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} {{ $p->is_active ? '(Aktif)' : '' }}</option>
                @endforeach
            </select>

            <!-- Filter Prodi -->
            <select wire:model.live="selected_prodi_id" class="px-4 py-2 rounded-xl border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-sm focus:ring-brand-blue focus:border-brand-blue">
                <option value="">Semua Program Studi</option>
                @foreach($prodis as $prodi)
                    <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- 3. STATS OVERVIEW -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Mahasiswa -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-brand-blue" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                {{ $selected_prodi_id ? 'Mhs Aktif (Prodi Terpilih)' : 'Total Mahasiswa Aktif' }}
            </p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-2">{{ number_format($total_mhs) }}</h3>
            <p class="text-sm text-green-600 font-bold mt-2">Terdaftar</p>
        </div>

        <!-- Dosen -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-purple-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l11 6 9-4.91V17h2V9M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">
                 {{ $selected_prodi_id ? 'Dosen (Prodi Terpilih)' : 'Total Dosen' }}
            </p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-2">{{ number_format($total_dosen) }}</h3>
            <p class="text-sm text-slate-500 font-medium mt-2">Pengajar Tetap & LB</p>
        </div>

        <!-- Prodi (Tetap statis karena ini jumlah global jurusan) -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M4 10v7h3v-7H4zm6 0v7h3v-7h-3zM2 22h19v-3H2v3zm14-12v7h3v-7h-3zm-4.5-9L2 6v2h19V6l-9.5-5z"/></svg>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Program Studi</p>
            <h3 class="text-4xl font-black text-slate-800 dark:text-white mt-2">{{ $total_prodi }}</h3>
            <p class="text-sm text-slate-500 font-medium mt-2">Fakultas & Jurusan</p>
        </div>
    </div>

    <!-- 4. CHARTS ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart: Mahasiswa Per Prodi -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h4 class="font-bold text-lg text-slate-800 dark:text-white mb-6">Sebaran Mahasiswa per Prodi</h4>
            <div id="prodiChart" class="h-80"></div>
        </div>

        <!-- Chart: Keuangan -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex flex-col">
            <h4 class="font-bold text-lg text-slate-800 dark:text-white mb-1">Status Keuangan</h4>
            <p class="text-xs text-slate-500 mb-6">
                Data berdasarkan Semester: 
                <strong class="text-brand-blue">{{ $periods->firstWhere('id', $selected_period_id)->name ?? '-' }}</strong>
            </p>
            
            <div class="flex-1 flex items-center justify-center">
                <div id="financeChart" class="w-full"></div>
            </div>
        </div>
    </div>

    <!-- 5. AKTIVITAS (Sama seperti sebelumnya, disembunyikan untuk ringkas) -->

    <!-- Scripts Chart -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('livewire:navigated', () => {
            // Assign data safely to prevent syntax errors
            const chartData = {
                prodiValues: @json($chart_prodi_values),
                prodiLabels: @json($chart_prodi_labels),
                financeStats: @json($finance_stats)
            };

            let chartProdi, chartFinance;

            const initCharts = (prodiValues, prodiLabels, financeStats) => {
                // 1. Chart Prodi
                const optionsProdi = {
                    series: [{ name: 'Jumlah Mahasiswa', data: prodiValues }],
                    chart: { type: 'bar', height: 320, fontFamily: 'Plus Jakarta Sans, sans-serif', toolbar: { show: false }, animations: { enabled: true } },
                    plotOptions: { bar: { borderRadius: 6, horizontal: true, barHeight: '60%' } },
                    dataLabels: { enabled: false },
                    colors: ['#1a237e'],
                    xaxis: { categories: prodiLabels },
                    grid: { borderColor: '#f1f5f9' },
                    tooltip: { theme: 'dark' }
                };
                
                if(chartProdi) chartProdi.destroy();
                chartProdi = new ApexCharts(document.querySelector("#prodiChart"), optionsProdi);
                chartProdi.render();

                // 2. Chart Keuangan
                const optionsFinance = {
                    series: financeStats,
                    chart: { type: 'donut', height: 250, fontFamily: 'Plus Jakarta Sans, sans-serif', animations: { enabled: true } },
                    labels: ['Lunas', 'Belum Lunas'],
                    colors: ['#10b981', '#ef4444'],
                    plotOptions: { pie: { donut: { size: '70%', labels: { show: true, total: { show: true, label: 'Tagihan', fontSize: '12px', fontWeight: 'bold', color: '#94a3b8' } } } } },
                    dataLabels: { enabled: false },
                    legend: { position: 'bottom' },
                    stroke: { show: false },
                    tooltip: { theme: 'dark' }
                };

                if(chartFinance) chartFinance.destroy();
                chartFinance = new ApexCharts(document.querySelector("#financeChart"), optionsFinance);
                chartFinance.render();
            };

            // Initialize First Load
            initCharts(chartData.prodiValues, chartData.prodiLabels, chartData.financeStats);

            // Listen for Livewire updates
            Livewire.on('update-charts', (data) => {
                // In Livewire 3, the payload is passed directly as 'data' (the object),
                // or sometimes wrapped if multiple arguments are sent. 
                // We check if it's the object we expect.
                const payload = data; 
                
                if (chartProdi && payload.prodi_values) {
                    chartProdi.updateSeries([{ data: payload.prodi_values }]);
                    chartProdi.updateOptions({ xaxis: { categories: payload.prodi_labels } });
                }
                
                if (chartFinance && payload.finance_stats) {
                    chartFinance.updateSeries(payload.finance_stats);
                }
            });
        });
    </script>
</div>