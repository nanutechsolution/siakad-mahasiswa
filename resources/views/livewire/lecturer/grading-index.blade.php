<div class="mx-auto max-w-7xl space-y-6">
    <x-slot name="header">Input Nilai</x-slot>

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Daftar Kelas Semester Ini</h1>
            <p class="text-slate-500">Pilih kelas untuk mulai mengisi atau mengedit nilai mahasiswa.</p>
        </div>
        <div class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg text-sm font-bold border border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">
            Periode: {{ $period->name ?? '-' }}
        </div>
    </div>

    @if(collect($classes)->isEmpty())
        <div class="p-12 text-center border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 dark:bg-slate-800/50 dark:border-slate-700">
            <div class="mx-auto w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mb-4 dark:bg-slate-700">
                <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-700 dark:text-white">Tidak Ada Kelas</h3>
            <p class="text-slate-500">Anda tidak memiliki jadwal kelas di semester aktif ini.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
            <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:border-indigo-500 transition-all hover:shadow-md cursor-pointer">
                <a href="{{ route('lecturer.grading', $class->id) }}" class="absolute inset-0 z-10"></a>
                
                <div class="flex justify-between items-start mb-4">
                    <span class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1 rounded dark:bg-slate-700 dark:text-slate-300">
                        Kelas {{ $class->name }}
                    </span>
                    @if($class->progress >= 100)
                        <span class="text-green-600 text-xs font-bold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Selesai
                        </span>
                    @else
                        <span class="text-xs font-bold text-orange-500">{{ $class->progress }}% Dinilai</span>
                    @endif
                </div>

                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-1 group-hover:text-indigo-600 transition-colors">
                    {{ $class->course->name }}
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                    {{ $class->course->code }} • {{ $class->study_plans->count() }} Mahasiswa
                </p>

                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 rounded-full h-2 dark:bg-slate-700 mb-4 overflow-hidden">
                    <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500" style="width: {{ $class->progress }}%"></div>
                </div>

                <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-100 dark:border-slate-700">
                    <span class="text-xs text-slate-400 font-medium">Klik untuk input nilai &rarr;</span>
                    <div class="h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors dark:bg-indigo-900/30 dark:text-indigo-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
