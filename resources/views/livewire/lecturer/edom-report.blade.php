<div class="mx-auto max-w-5xl space-y-8">
    <x-slot name="header">Rapor Kinerja Dosen (EDOM)</x-slot>

    <!-- Score Card Utama -->
    <div class="flex flex-col md:flex-row items-center justify-between bg-slate-900 dark:bg-slate-800 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-blue/30 rounded-full blur-3xl -mr-10 -mt-10"></div>
        
        <div class="relative z-10">
            <h2 class="text-2xl font-bold">Indeks Kinerja Semester</h2>
            <p class="text-slate-300 mt-1">Berdasarkan evaluasi mahasiswa pada semester {{ $period->name ?? '-' }}</p>
        </div>

        <div class="relative z-10 flex items-center gap-4 mt-6 md:mt-0">
            <div class="text-right">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Skor Rata-rata</p>
                <p class="text-5xl font-black text-brand-gold">{{ $overall_score }}</p>
            </div>
            <div class="text-xs font-bold bg-white/10 px-3 py-1 rounded text-slate-300">
                Skala 5.00
            </div>
        </div>
    </div>

    <!-- Detail per Kategori -->
    <div class="grid grid-cols-1 gap-6">
        @foreach($report as $category => $questions)
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-700/50 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 dark:text-white uppercase tracking-wider text-sm flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-brand-blue rounded-full"></span>
                    {{ $category }}
                </h3>
                <span class="text-xs font-bold bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 px-2 py-1 rounded">
                    Rata-rata: {{ number_format($questions->avg('average_score'), 2) }}
                </span>
            </div>
            
            <div class="p-6 space-y-5">
                @foreach($questions as $q)
                <div>
                    <div class="flex justify-between items-end mb-1">
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 w-3/4">{{ $q->question_text }}</p>
                        <span class="text-sm font-bold {{ $q->average_score >= 4 ? 'text-green-600' : ($q->average_score >= 3 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $q->average_score }}
                        </span>
                    </div>
                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                        <div class="h-2.5 rounded-full {{ $q->average_score >= 4 ? 'bg-green-500' : ($q->average_score >= 3 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                             style="width: {{ ($q->average_score / 5) * 100 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>