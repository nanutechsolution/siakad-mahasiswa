<div>
    <x-slot name="header">Hasil Evaluasi Dosen (EDOM)</x-slot>

    <!-- Header Info -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-6 gap-4">
        <div>
            <h2 class="font-bold text-xl text-slate-800 dark:text-white">Rapor Kinerja Dosen</h2>
            <p class="text-slate-500 text-sm">Periode Akademik: <span class="font-bold text-brand-blue">{{ $active_period->name ?? '-' }}</span></p>
        </div>
        
        <div class="flex gap-2">
            <select wire:model.live="filter_prodi" class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white text-sm">
                <option value="">Semua Prodi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Dosen..." class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white text-sm w-64">
        </div>
    </div>

    <!-- Tabel Ranking -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4 w-12 text-center">Rank</th>
                    <th class="px-6 py-4">Nama Dosen</th>
                    <th class="px-6 py-4">Prodi Homebase</th>
                    <th class="px-6 py-4 text-center">Jml Responden</th>
                    <th class="px-6 py-4 text-center">Skor Rata-rata</th>
                    <th class="px-6 py-4 text-center">Kategori</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($lecturers as $index => $l)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-6 py-4 text-center font-bold text-slate-400">
                        #{{ $loop->iteration + ($lecturers->currentPage() - 1) * $lecturers->perPage() }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $l->user->name }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $l->nidn }}</div>
                    </td>
                    <td class="px-6 py-4">
                        {{ $l->study_program->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded text-xs font-bold text-slate-600 dark:text-slate-300">
                            {{ $l->total_respondents }} Data
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $score = number_format($l->avg_score, 2);
                            $color = $score >= 4.0 ? 'text-green-600' : ($score >= 3.0 ? 'text-yellow-600' : 'text-red-600');
                        @endphp
                        <span class="text-2xl font-black {{ $color }}">{{ $score }}</span>
                        <span class="text-xs text-slate-400">/ 5.00</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($l->avg_score >= 4.5)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                ⭐ SANGAT BAIK
                            </span>
                        @elseif($l->avg_score >= 4.0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-700 border border-teal-200">
                                😊 BAIK
                            </span>
                        @elseif($l->avg_score >= 3.0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                😐 CUKUP
                            </span>
                        @elseif($l->avg_score > 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                ⚠️ KURANG
                            </span>
                        @else
                            <span class="text-xs text-slate-400 italic">Belum ada data</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        Belum ada data evaluasi untuk periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">
            {{ $lecturers->links() }}
        </div>
    </div>
</div>