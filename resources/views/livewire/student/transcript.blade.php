<div class="mx-auto max-w-7xl space-y-8">
    <x-slot name="header">Transkrip Nilai</x-slot>

    @if (isset($error))
        <div
            class="p-12 text-center border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 dark:bg-slate-800/50 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-700 dark:text-white">Data Mahasiswa Tidak Ditemukan</h3>
        </div>
    @else
        <!-- 1. HEADER STATISTIK (IPK CARD) -->
        <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 p-8 text-white shadow-2xl">
            <div class="absolute right-0 top-0 h-full w-2/3 bg-gradient-to-l from-brand-blue to-transparent opacity-60">
            </div>
            <div class="absolute -right-20 -top-40 h-96 w-96 rounded-full bg-brand-gold/10 blur-[80px]"></div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                <div>
                    <h2 class="text-3xl font-black tracking-tight text-white mb-1">Transkrip Akademik</h2>
                    <p class="text-blue-200 font-medium">Rekapitulasi Nilai Kumulatif</p>

                    <div class="mt-6 flex gap-8">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Total SKS</p>
                            <p class="text-4xl font-black text-white">{{ $total_sks }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Total Mutu
                            </p>
                            <p class="text-4xl font-black text-white">{{ $total_bobot }}</p>
                        </div>
                    </div>
                </div>

                <!-- IPK BIG CIRCLE -->
                <div class="relative flex items-center justify-center">
                    <div
                        class="w-40 h-40 rounded-full border-4 border-brand-gold/30 flex items-center justify-center bg-slate-800/50 backdrop-blur-sm shadow-lg">
                        <div class="text-center">
                            <p class="text-xs font-bold text-brand-gold uppercase tracking-wider">IPK</p>
                            <p class="text-5xl font-black text-white tracking-tighter">{{ $ipk }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. TOMBOL CETAK -->
        <div class="flex justify-end ">
            <a href="{{ route('student.print.transcript') }}" target="_blank"
                class="flex items-center gap-2 rounded-xl bg-brand-blue px-6 py-3 text-sm font-bold text-white transition-all hover:bg-blue-800 shadow-lg shadow-blue-900/20 hover:-translate-y-1">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak Transkrip (PDF)
            </a>
        </div>

        <!-- 3. TABEL NILAI (GROUPED BY SEMESTER) -->
        <div class="space-y-8">
            @foreach ($grouped_grades as $semester_name => $grades)
                <div
                    class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800 overflow-hidden">
                    <div
                        class="bg-slate-50 px-6 py-4 border-b border-slate-100 dark:bg-slate-700/50 dark:border-slate-700">
                        <h3 class="font-bold text-slate-800 dark:text-white text-lg flex items-center gap-2">
                            <span class="w-2 h-6 bg-brand-blue rounded-full"></span>
                            Semester {{ $semester_name }}
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead
                                class="bg-white text-slate-500 dark:bg-slate-800 dark:text-slate-400 border-b border-slate-100 dark:border-slate-700">
                                <tr>
                                    <th class="px-6 py-3 font-semibold ">Kode</th>
                                    <th class="px-6 py-3 font-semibold">Mata Kuliah</th>
                                    <th class="px-6 py-3 font-semibold text-center w-20">SKS</th>
                                    <th class="px-6 py-3 font-semibold text-center w-20">Nilai</th>
                                    <th class="px-6 py-3 font-semibold text-center w-20">Bobot</th>
                                    <th class="px-6 py-3 font-semibold text-center w-20">Mutu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                @foreach ($grades as $grade)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors">
                                        <td class="px-6 py-4 font-mono text-xs text-slate-500">
                                            {{ $grade->classroom->course->code }}</td>
                                        <td class="px-6 py-4 font-medium text-slate-800 dark:text-white">
                                            {{ $grade->classroom->course->name }}</td>
                                        <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400">
                                            {{ $grade->classroom->course->credit_total }}</td>
                                        <td
                                            class="px-6 py-4 text-center font-bold {{ $grade->grade_letter == 'A' ? 'text-green-600' : 'text-slate-800 dark:text-white' }}">
                                            {{ $grade->grade_letter }}
                                        </td>
                                        <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-400">
                                            {{ $grade->grade_point }}</td>
                                        <td
                                            class="px-6 py-4 text-center font-bold text-brand-blue dark:text-brand-gold">
                                            {{ $grade->classroom->course->credit_total * $grade->grade_point }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 dark:bg-slate-700/30">
                                <tr>
                                    <td colspan="2"
                                        class="px-6 py-3 text-right font-bold text-slate-500 text-xs uppercase tracking-wider">
                                        Subtotal Semester</td>
                                    <td class="px-6 py-3 text-center font-bold text-slate-800 dark:text-white">
                                        {{ $grades->sum(fn($g) => $g->classroom->course->credit_total) }}</td>
                                    <td colspan="2"></td>
                                    <td class="px-6 py-3 text-center font-bold text-brand-blue dark:text-brand-gold">
                                        {{ $grades->sum(fn($g) => $g->classroom->course->credit_total * $g->grade_point) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach

            @if ($grouped_grades->isEmpty())
                <div class="text-center py-12 text-slate-500 border-2 border-dashed border-slate-200 rounded-xl">
                    Belum ada nilai yang masuk transkrip.
                </div>
            @endif
        </div>
    @endif
</div>
