<div class="mx-auto max-w-6xl space-y-6">
    <x-slot name="header">Input Nilai Mahasiswa</x-slot>

    <!-- Header Kelas -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div>
            <div class="flex items-center gap-3">
                <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-2 py-1 rounded dark:bg-indigo-900/50 dark:text-indigo-300">
                    {{ $classroom->name }}
                </span>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $classroom->course->name }}</h2>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                {{ $classroom->course->code }} • {{ $classroom->course->credit_total }} SKS • Semester {{ $classroom->academic_period->name }}
            </p>
        </div>
        
        <div class="flex gap-3">
            <div class="text-right">
                <p class="text-xs text-slate-400 uppercase font-bold">Total Mahasiswa</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ count($students) }}</p>
            </div>
            <div class="w-px bg-slate-200 dark:bg-slate-700 mx-2"></div>
            <a href="{{ route('lecturer.dashboard') }}" class="flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-bold transition-colors dark:bg-slate-700 dark:text-slate-300 dark:hover:bg-slate-600">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Tabel Input Nilai -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        
        <!-- Header Info -->
        <div class="p-4 bg-yellow-50 border-b border-yellow-100 text-xs text-yellow-800 flex items-start gap-2 dark:bg-yellow-900/20 dark:border-yellow-900/30 dark:text-yellow-400">
            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <p>
                <strong>Instruksi:</strong> Masukkan nilai akhir (Angka 0-100) pada kolom "Nilai Angka". Sistem akan otomatis menyimpan dan mengonversi ke Nilai Huruf saat Anda menekan Enter atau berpindah baris.
            </p>
        </div>

        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 uppercase text-xs font-bold border-b border-slate-200 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-4 w-12 text-center">#</th>
                    <th class="px-6 py-4">Mahasiswa</th>
                    <th class="px-6 py-4 text-center w-40">Nilai Angka<br><span class="text-[10px] font-normal lowercase">(0 - 100)</span></th>
                    <th class="px-6 py-4 text-center w-32">Nilai Huruf</th>
                    <th class="px-6 py-4 text-center w-32">Bobot</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($students as $id => $data)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                    <td class="px-6 py-4 text-center text-slate-400">
                        {{ $loop->iteration }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white text-base">
                            {{ $data['name'] }}
                        </div>
                        <div class="text-xs text-slate-500 font-mono mt-0.5">
                            {{ $data['nim'] }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <!-- Input Angka -->
                        <div class="relative">
                            <input type="number" 
                                   wire:model.live.debounce.1000ms="students.{{ $id }}.score_number" 
                                   class="w-full text-center text-lg font-bold rounded-lg border-slate-300 focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-600 dark:text-white placeholder-slate-300 disabled:opacity-50 transition-all"
                                   min="0" max="100" step="0.01" placeholder="0">
                            
                            <!-- Indikator Loading per baris -->
                            <div wire:loading wire:target="students.{{ $id }}.score_number" class="absolute right-2 top-3">
                                <svg class="animate-spin h-4 w-4 text-brand-blue" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <!-- Display Huruf -->
                        @php
                            $colorClass = match($data['grade_letter']) {
                                'A', 'A-' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                                'B+', 'B', 'B-' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                'C+', 'C' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                                'D', 'E' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                default => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400'
                            };
                        @endphp
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-lg font-black {{ $colorClass }}">
                            {{ $data['grade_letter'] ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-mono text-slate-600 dark:text-slate-400 font-bold">
                        {{ $data['grade_point'] ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-12 text-center text-slate-500">
                        <div class="mx-auto w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4 dark:bg-slate-700">
                            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <h3 class="font-bold text-lg text-slate-700 dark:text-white">Belum ada Mahasiswa</h3>
                        <p>Belum ada mahasiswa yang KRS-nya disetujui (ACC) untuk kelas ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
