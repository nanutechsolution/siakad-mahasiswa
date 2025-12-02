<div class="mx-auto max-w-7xl space-y-8">
    <x-slot name="header">Dashboard Dosen</x-slot>

    <!-- 1. Header Ringkas -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
                Selamat Datang, {{ Auth::user()->name }}
            </h1>
            <p class="text-slate-500">
                Semester Aktif: <span class="font-bold text-brand-blue">{{ $period->name ?? '-' }}</span>
            </p>
        </div>
        
        <!-- Statistik Ringkas -->
        <div class="flex gap-4">
            <div class="bg-white dark:bg-slate-800 px-4 py-2 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                <p class="text-xs text-slate-500 uppercase font-bold">Total Kelas</p>
                <p class="text-xl font-bold text-slate-800 dark:text-white">{{ $classes->count() }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 px-4 py-2 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                <p class="text-xs text-slate-500 uppercase font-bold">Total Mhs</p>
                <p class="text-xl font-bold text-slate-800 dark:text-white">
                    {{ $classes->sum(fn($c) => $c->study_plans->count()) }}
                </p>
            </div>
        </div>
    </div>

    <!-- 2. Grid Jadwal Mengajar -->
    @if(collect($classes)->isEmpty())
        <div class="p-12 text-center border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 dark:bg-slate-800/50 dark:border-slate-700">
            <div class="mx-auto w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mb-4 dark:bg-slate-700">
                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-700 dark:text-white">Jadwal Kosong</h3>
            <p class="text-slate-500">Anda tidak memiliki jadwal mengajar di semester aktif ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
            <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:border-indigo-500 transition-all hover:shadow-md">
                
                <!-- Label Kelas -->
                <div class="flex justify-between items-start mb-4">
                    <span class="bg-indigo-50 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full dark:bg-indigo-900/30 dark:text-indigo-300">
                        Kelas {{ $class->name }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">{{ $class->course->code }}</span>
                </div>

                <!-- Info Matkul -->
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 transition-colors">
                    {{ $class->course->name }}
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                    {{ $class->course->credit_total }} SKS • {{ $class->study_plans->count() }} Mahasiswa
                </p>

                <!-- List Jadwal Hari & Jam -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-700 space-y-2">
                    @foreach($class->schedules as $s)
                        <div class="flex items-center gap-3 text-xs text-slate-600 dark:text-slate-300">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 font-bold text-slate-700 dark:text-slate-200">
                                {{ substr($s->day, 0, 3) }}
                            </div>
                            <div>
                                <p class="font-bold">
                                    {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}
                                </p>
                                <p class="text-slate-400">Ruang {{ $s->room_name }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
            @endforeach
        </div>
    @endif
</div>