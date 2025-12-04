<div>
    <x-slot name="header">Dashboard Penerimaan Mahasiswa Baru</x-slot>

    <!-- 1. STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total Akun -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-600 rounded-lg">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 uppercase font-bold">Total Pendaftar</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $total_pendaftar }}</h3>
                </div>
            </div>
        </div>

        <!-- Formulir Masuk -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 rounded-lg">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 uppercase font-bold">Formulir Masuk</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $total_submit }}</h3>
                </div>
            </div>
        </div>

        <!-- Lulus Seleksi -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-50 dark:bg-green-900/20 text-green-600 rounded-lg">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 uppercase font-bold">Lulus Seleksi</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $total_diterima }}</h3>
                </div>
            </div>
        </div>

        <!-- Daftar Ulang -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-50 dark:bg-purple-900/20 text-purple-600 rounded-lg">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500 uppercase font-bold">Jadi Mahasiswa</p>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $total_daftar_ulang }}</h3>
                </div>
            </div>
        </div>

    </div>

    <!-- 2. CHARTS ROW -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- GRAFIK PENDAFTAR HARIAN -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-4">Tren Pendaftaran (7 Hari Terakhir)</h3>
            <div id="dailyChart" class="h-72"></div>
        </div>

        <!-- TOP PRODI -->
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-700">
            <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-4">Minat Program Studi</h3>
            
            <div class="space-y-4">
                @foreach($stats_prodi as $stat)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $stat->firstChoice->name }}</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $stat->total }}</span>
                    </div>
                    @php
                        $percent = $total_submit > 0 ? ($stat->total / $total_submit) * 100 : 0;
                    @endphp
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-brand-blue h-2 rounded-full" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
                @endforeach
                
                @if($stats_prodi->isEmpty())
                    <p class="text-center text-slate-400 text-sm py-4">Belum ada data pendaftar.</p>
                @endif
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:navigated', () => {
        
        var options = {
            series: [{
                name: "Pendaftar Baru",
                data: @json($chart_values)
            }],
            chart: {
                type: 'area',
                height: 300,
                fontFamily: 'Inter, sans-serif',
                toolbar: { show: false }
            },
            colors: ['#1a237e'], // Brand Blue
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: @json($chart_labels),
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { show: false },
            grid: { show: false },
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            theme: {
                mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
            }
        };

        var chart = new ApexCharts(document.querySelector("#dailyChart"), options);
        chart.render();
    });
</script>