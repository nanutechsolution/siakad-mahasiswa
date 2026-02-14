<div class="space-y-6 font-sans">
    <x-slot name="header">Admin Dashboard</x-slot>

    <!-- Filters -->
    <div class="flex flex-wrap gap-4 bg-white dark:bg-slate-800 p-4 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Periode Akademik</label>
            <select wire:model.live="selected_period_id" class="w-full rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 text-sm focus:ring-brand-blue">
                @foreach($periods as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Program Studi</label>
            <select wire:model.live="selected_prodi_id" class="w-full rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 text-sm focus:ring-brand-blue">
                <option value="">Semua Program Studi</option>
                @foreach($prodis as $pr)
                <option value="{{ $pr->id }}">{{ $pr->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mahasiswa Aktif</p>
            <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($total_mhs) }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dosen Aktif</p>
            <h3 class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ number_format($total_dosen) }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Lembar KRS</p>
            <h3 class="text-3xl font-black text-brand-blue mt-1">{{ number_format($krs_count) }}</h3>
        </div>
        <div class="bg-brand-blue p-6 rounded-[2rem] text-white shadow-xl shadow-blue-900/20">
            <p class="text-[10px] font-black text-blue-200 uppercase tracking-widest">Pendapatan (Paid)</p>
            <h3 class="text-2xl font-black mt-1">Rp {{ number_format($finance[0] ?? 0, 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2.5rem] border border-slate-200 dark:border-slate-700 shadow-sm">
            <h4 class="font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                <span class="w-2 h-5 bg-brand-blue rounded-full"></span> Sebaran Mahasiswa per Prodi
            </h4>
            <div class="h-64" wire:ignore>
                <canvas id="prodiChart"></canvas>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2.5rem] border border-slate-200 dark:border-slate-700 shadow-sm">
            <h4 class="font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
                <span class="w-2 h-5 bg-green-500 rounded-full"></span> Monitoring Status KRS
            </h4>
            <div class="h-64" wire:ignore>
                <canvas id="krsChart"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctxProdi = document.getElementById('prodiChart');
            const ctxKrs = document.getElementById('krsChart');

            let prodiChart = new Chart(ctxProdi, {
                type: 'bar',
                data: {
                    labels: @json($chart_prodi['labels'] ?? []),
                    datasets: [{ label: 'Mahasiswa', data: @json($chart_prodi['values'] ?? []), backgroundColor: '#3b82f6', borderRadius: 8 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });

            let krsChart = new Chart(ctxKrs, {
                type: 'doughnut',
                data: {
                    labels: @json($chart_krs['labels'] ?? []),
                    datasets: [{ data: @json($chart_krs['values'] ?? []), backgroundColor: ['#94a3b8', '#3b82f6', '#22c55e'] }]
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
            });

            window.Livewire.on('update-charts', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                
                if (data.prodi_data) {
                    prodiChart.data.labels = data.prodi_data.labels;
                    prodiChart.data.datasets[0].data = data.prodi_data.values;
                    prodiChart.update();
                }

                if (data.krs_data) {
                    krsChart.data.labels = data.krs_data.labels;
                    krsChart.data.datasets[0].data = data.krs_data.values;
                    krsChart.update();
                }
            });
        });
    </script>
</div>