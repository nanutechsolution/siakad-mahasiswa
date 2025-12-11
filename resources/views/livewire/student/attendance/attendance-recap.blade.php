<div class="mx-auto max-w-5xl space-y-6 font-sans">
    <x-slot name="header">Riwayat Kehadiran</x-slot>

    <!-- Header Info -->
    <div class="rounded-[2.5rem] bg-indigo-900 p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold">Monitor Kehadiran</h2>
            <p class="text-indigo-200 mt-2">
                Pastikan kehadiran Anda di atas <strong>75%</strong> untuk dapat mengikuti Ujian Akhir Semester (UAS).
            </p>
        </div>
    </div>

    <!-- List Mata Kuliah -->
    <div class="grid grid-cols-1 gap-6" x-data="{ activeAccordion: null }">
        @forelse($recap as $index => $item)
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-all duration-300">
            
            <!-- Card Header (Klik untuk Expand) -->
            <button @click="activeAccordion = activeAccordion === {{ $index }} ? null : {{ $index }}" 
                    class="w-full flex flex-col md:flex-row md:items-center justify-between p-6 text-left hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                
                <!-- Info Kiri -->
                <div class="flex items-center gap-4 mb-4 md:mb-0">
                    <div class="h-12 w-12 rounded-full flex items-center justify-center font-bold text-sm bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300">
                        {{ substr($item['course_name'], 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-slate-900 dark:text-white">{{ $item['course_name'] }}</h3>
                        <p class="text-sm text-slate-500">{{ $item['course_code'] }} • Kelas {{ $item['class_name'] }}</p>
                    </div>
                </div>

                <!-- Info Kanan (Progress) -->
                <div class="flex items-center gap-6 w-full md:w-auto">
                    <div class="flex-1 md:w-48">
                        <div class="flex justify-between text-xs font-bold mb-1 uppercase tracking-wider text-slate-400">
                            <span>Kehadiran</span>
                            <span>{{ $item['present'] }}/{{ $item['total'] }}</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $item['color'] }}" style="width: {{ $item['percent'] }}%"></div>
                        </div>
                    </div>
                    <div class="text-right min-w-[3rem]">
                        <span class="text-xl font-black text-slate-800 dark:text-white">{{ $item['percent'] }}%</span>
                    </div>
                    <!-- Icon Chevron -->
                    <svg class="w-5 h-5 text-slate-400 transform transition-transform duration-300" 
                         :class="activeAccordion === {{ $index }} ? 'rotate-180' : ''" 
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </button>

            <!-- Accordion Body (Detail Pertemuan) -->
            <div x-show="activeAccordion === {{ $index }}" x-collapse class="border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                <div class="p-6">
                    <h4 class="text-xs font-bold uppercase text-slate-400 mb-4 tracking-wider">Detail Pertemuan</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($item['history'] as $meet)
                        <div class="bg-white dark:bg-slate-800 p-3 rounded-xl border border-slate-200 dark:border-slate-700 flex items-center gap-3">
                            <!-- Badge Status -->
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center font-bold text-sm
                                {{ $meet['status'] == 'H' ? 'bg-green-100 text-green-700' : 
                                  ($meet['status'] == 'A' ? 'bg-red-100 text-red-700' : 
                                  ($meet['status'] == '-' ? 'bg-slate-100 text-slate-400' : 'bg-yellow-100 text-yellow-700')) }}">
                                {{ $meet['status'] }}
                            </div>
                            
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-500 uppercase">Pertemuan {{ $meet['no'] }}</p>
                                <p class="text-xs font-medium text-slate-800 dark:text-white truncate" title="{{ $meet['topic'] }}">
                                    {{ $meet['date'] }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-4 flex gap-4 text-[10px] text-slate-400 uppercase font-bold tracking-wider">
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Hadir</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Izin/Sakit</span>
                        <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Alpha</span>
                    </div>
                </div>
            </div>

        </div>
        @empty
        <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
            <p class="text-slate-500">Belum ada data kehadiran untuk semester ini.</p>
        </div>
        @endforelse
    </div>
</div>