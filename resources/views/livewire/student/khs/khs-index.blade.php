<div class="mx-auto max-w-5xl space-y-8">
    <x-slot name="header">Kartu Hasil Studi</x-slot>

    <!-- Header Info -->
    <div class="rounded-[2rem] bg-white p-8 shadow-sm border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Riwayat Hasil Studi</h2>
        <p class="text-slate-500 mt-2">Berikut adalah daftar Indeks Prestasi Semester (IPS) yang telah Anda tempuh.</p>
    </div>

    <!-- List Semester -->
    <div class="grid grid-cols-1 gap-4">
        @forelse($history_periods as $period)
        <div class="group flex flex-col md:flex-row md:items-center justify-between rounded-2xl bg-white p-6 shadow-sm border border-slate-100 transition-all hover:border-brand-blue hover:shadow-md dark:bg-slate-800 dark:border-slate-700 dark:hover:border-brand-gold">
            
            <div class="flex items-center gap-4 mb-4 md:mb-0">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 font-black text-lg dark:bg-slate-700 dark:text-indigo-400">
                    {{ substr($period->code, -1) == '1' ? 'G' : 'G' }} <!-- Ganjil/Genap Icon -->
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Semester {{ $period->name }}</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Tahun Ajaran {{ substr($period->code, 0, 4) }}</p>
                </div>
            </div>

            <div class="flex items-center gap-8">
                <div class="text-center">
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">SKS</p>
                    <p class="text-xl font-bold text-slate-800 dark:text-white">{{ $period->total_sks }}</p>
                </div>
                <div class="text-center">
                    <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">IPS</p>
                    <p class="text-xl font-black text-brand-gold">{{ $period->ips }}</p>
                </div>
                
                <!-- Tombol Cetak -->
                <a href="{{ route('student.print.khs', ['period_id' => $period->id]) }}" target="_blank" 
                   class="flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-bold text-white transition-all hover:bg-brand-blue shadow-lg shadow-slate-900/20 dark:bg-slate-700 dark:hover:bg-brand-blue">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-12 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 dark:bg-slate-800/50 dark:border-slate-700">
            <p class="text-slate-500 font-medium">Belum ada riwayat studi yang selesai dinilai.</p>
        </div>
        @endforelse
    </div>
</div>