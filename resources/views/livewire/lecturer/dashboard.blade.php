<div class="mx-auto max-w-7xl space-y-8 font-sans text-slate-600 dark:text-slate-300">
    <x-slot name="header">Dashboard Dosen</x-slot>

    <!-- Header & Summary -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                Selamat Datang, {{ Auth::user()->name }}
            </h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">
                Semester Aktif: <span class="font-bold text-brand-blue dark:text-brand-gold">{{ $period->name ?? '-' }}</span>
            </p>
        </div>
        
        <!-- Statistik Ringkas -->
        <div class="flex gap-4">
            <div class="bg-white dark:bg-slate-800 px-5 py-3 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Kelas</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">{{ $classes->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 px-5 py-3 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Total Mhs</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white">
                    {{ $classes->sum(fn($c) => $c->study_plans->count()) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Grid Jadwal Mengajar -->
    @if(collect($classes)->isEmpty())
        <div class="p-12 text-center border-2 border-dashed border-slate-300 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-800/50">
            <div class="mx-auto w-16 h-16 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-700 dark:text-white">Jadwal Kosong</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Anda tidak memiliki jadwal mengajar di semester aktif ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
            <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:border-brand-blue dark:hover:border-brand-blue transition-all hover:shadow-lg flex flex-col h-full">
                
                <!-- Label Kelas & Kode -->
                <div class="flex justify-between items-start mb-4">
                    <span class="bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs font-bold px-3 py-1 rounded-full border border-indigo-100 dark:border-indigo-800">
                        Kelas {{ $class->name }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono border border-slate-200 dark:border-slate-600 px-2 py-0.5 rounded">
                        {{ $class->course->code }}
                    </span>
                </div>

                <!-- Info Matkul -->
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-brand-blue dark:group-hover:text-brand-gold transition-colors line-clamp-2">
                        {{ $class->course->name }}
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-4 flex items-center gap-2">
                        <span>{{ $class->course->credit_total }} SKS</span>
                        <span>•</span>
                        <span>{{ $class->study_plans->count() }} Mahasiswa</span>
                    </p>

                    <!-- List Jadwal Hari & Jam -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 space-y-3">
                        @foreach($class->schedules as $s)
                            <div class="flex items-center gap-3 text-xs text-slate-600 dark:text-slate-300">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 font-bold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-600">
                                    {{ substr($s->day, 0, 3) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 dark:text-white">
                                        {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}
                                    </p>
                                    <p class="text-slate-400">Ruang {{ $s->room_name }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- TOMBOL NAVIGASI (INPUT NILAI & PRESENSI) -->
                <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 flex gap-2">
                    
                    <!-- Tombol Input Nilai -->
                    <a href="{{ route('lecturer.grading', $class->id) }}" 
                       class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl text-sm font-bold hover:bg-brand-blue dark:hover:bg-slate-200 transition-colors shadow-lg shadow-slate-900/10">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        Input Nilai
                    </a>

                    <!-- Tombol Presensi (Opsional, jika nanti ada) -->
                    <a href="{{ route('lecturer.attendance', $class->id) }}" 
                       class="px-3 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors border border-slate-200 dark:border-slate-600" title="Presensi">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    </a>

                </div>

            </div>
            @endforeach
        </div>
    @endif
</div>