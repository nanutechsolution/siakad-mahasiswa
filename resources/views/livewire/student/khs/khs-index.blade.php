<div class="mx-auto max-w-5xl space-y-8 relative">
    <x-slot name="header">Kartu Hasil Studi</x-slot>

    <!-- GATEKEEPER EDOM (BLOKIR JIKA BELUM ISI) -->
    @if($edom_pending_count > 0)
        <div class="absolute inset-0 z-50 bg-white/80 dark:bg-slate-900/90 backdrop-blur-md flex items-start justify-center pt-20 rounded-3xl">
            <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl shadow-2xl border-2 border-brand-blue max-w-lg text-center transform scale-105">
                <div class="mx-auto w-20 h-20 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center mb-6 animate-bounce">
                    <svg class="w-10 h-10 text-brand-blue dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                
                <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-2">
                    Hasil Studi Terkunci!
                </h2>
                <p class="text-slate-600 dark:text-slate-300 mb-6">
                    Anda masih memiliki <strong class="text-red-500 text-lg">{{ $edom_pending_count }}</strong> mata kuliah yang belum dievaluasi (EDOM). 
                    Silakan isi kuesioner kinerja dosen terlebih dahulu untuk membuka akses KHS.
                </p>

                <a href="{{ route('student.edom.list') }}" class="inline-flex items-center gap-2 px-8 py-3.5 bg-brand-blue hover:bg-blue-800 text-white font-bold rounded-xl shadow-lg shadow-blue-900/30 transition-all transform hover:-translate-y-1">
                    <span>Isi EDOM Sekarang</span>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </div>
        </div>
    @endif

    <!-- KONTEN UTAMA (Akan tertutup blur jika ada hutang EDOM) -->
    <div class="{{ $edom_pending_count > 0 ? 'filter blur-sm pointer-events-none select-none' : '' }}">
        
        <!-- Header Info -->
        <div class="rounded-[2rem] bg-white p-8 shadow-sm border border-slate-100 dark:bg-slate-800 dark:border-slate-700">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Riwayat Hasil Studi</h2>
            <p class="text-slate-500 mt-2">Berikut adalah daftar Indeks Prestasi Semester (IPS) yang telah Anda tempuh.</p>
        </div>

        <!-- List Semester -->
        <div class="grid grid-cols-1 gap-4 mt-6">
            @forelse($history_periods as $period)
            <div class="group flex flex-col md:flex-row md:items-center justify-between rounded-2xl bg-white p-6 shadow-sm border border-slate-100 transition-all hover:border-brand-blue hover:shadow-md dark:bg-slate-800 dark:border-slate-700 dark:hover:border-brand-gold">
                
                <div class="flex items-center gap-4 mb-4 md:mb-0">
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 font-black text-lg dark:bg-slate-700 dark:text-indigo-400">
                        {{ substr($period->code, -1) == '1' ? 'G' : 'G' }}
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
</div>