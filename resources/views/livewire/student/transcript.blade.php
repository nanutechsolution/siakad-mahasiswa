<div class="space-y-6 font-sans pb-10">
    <x-slot name="header">Transkrip Nilai Kumulatif</x-slot>

    <!-- Summary IPK -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-brand-blue p-6 rounded-[2rem] text-white shadow-xl shadow-blue-900/20">
            <p class="text-[10px] uppercase font-black text-blue-200 tracking-widest">IPK Kumulatif</p>
            <h3 class="text-4xl font-black mt-1">{{ $ipk }}</h3>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm">
            <p class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Total SKS Lulus</p>
            <h3 class="text-3xl font-black mt-1 text-slate-800 dark:text-white">{{ $total_sks }} <span class="text-sm font-bold text-slate-400">SKS</span></h3>
        </div>
        <div class="bg-white dark:bg-slate-800 p-6 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Aksi</p>
                <a href="{{ route('student.print.transcript') }}" target="_blank" class="mt-2 inline-flex items-center gap-2 text-sm font-bold text-brand-blue hover:underline">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak Transkrip
                </a>
            </div>
        </div>
    </div>

    <!-- Grouped Grades -->
    <div class="space-y-8">
        @forelse($grouped_grades as $semesterName => $grades)
            <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h4 class="font-black text-slate-700 dark:text-white uppercase text-xs tracking-widest">{{ $semesterName }}</h4>
                    <span class="text-[10px] font-bold text-slate-400">{{ $grades->sum(fn($g) => $g->courseClass->course->credit_total) }} SKS</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="text-slate-400 font-bold uppercase text-[10px] tracking-widest border-b border-slate-50 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-3">Kode</th>
                                <th class="px-6 py-3">Mata Kuliah</th>
                                <th class="px-6 py-3 text-center">SKS</th>
                                <th class="px-6 py-3 text-center">Nilai</th>
                                <th class="px-6 py-3 text-center">Bobot</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700 font-medium">
                            @foreach($grades as $grade)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20">
                                <td class="px-6 py-4 font-black text-slate-400 tracking-tighter">{{ $grade->courseClass->course->code }}</td>
                                <td class="px-6 py-4 text-slate-800 dark:text-white">{{ $grade->courseClass->course->name }}</td>
                                <td class="px-6 py-4 text-center">{{ $grade->courseClass->course->credit_total }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold {{ $grade->grade_point >= 2 ? 'text-green-500' : 'text-red-500' }}">
                                        {{ $grade->grade_letter ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-400">{{ number_format($grade->grade_point, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="py-20 text-center bg-white dark:bg-slate-800 rounded-[2.5rem] border-2 border-dashed border-slate-200 dark:border-slate-700">
                <p class="text-slate-400 italic">Belum ada data nilai kumulatif yang tersedia.</p>
            </div>
        @endforelse
    </div>
</div>