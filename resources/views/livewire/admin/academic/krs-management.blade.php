<div>
    <x-slot name="header">Input KRS Manual (Admin)</x-slot>

    <div class="mb-8 relative">
        @if(!$selectedStudent)
            <div class="bg-white dark:bg-slate-800 p-8 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 text-center max-w-2xl mx-auto">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Cari Mahasiswa</h3>
                <p class="text-slate-500 mb-6">Cari berdasarkan Nama atau NIM untuk mengelola KRS mereka.</p>
                
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search_student" type="text" 
                           class="w-full rounded-lg border-slate-300 px-4 py-3 pl-12 text-lg shadow-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-700 dark:text-white" 
                           placeholder="Ketik Nama / NIM...">
                    <div class="absolute left-4 top-3.5 text-slate-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>

                    @if(count($students_result) > 0)
                        <div class="absolute z-10 mt-2 w-full rounded-lg border border-slate-200 bg-white shadow-xl dark:bg-slate-800 dark:border-slate-700 text-left overflow-hidden">
                            @foreach($students_result as $res)
                                <button wire:click="selectStudent('{{ $res->id }}')" class="block w-full px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 border-b border-slate-100 dark:border-slate-700 last:border-0 transition-colors">
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $res->user->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $res->nim }} • {{ $res->study_program->name ?? '-' }}</div>
                                </button>
                            @endforeach
                        </div>
                    @elseif(strlen($search_student) > 2)
                        <div class="absolute z-10 mt-2 w-full p-4 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-slate-200 dark:border-slate-700 text-slate-500">
                            Tidak ditemukan.
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="flex items-center justify-between bg-brand-blue text-white p-6 rounded-xl shadow-lg mb-6">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-full bg-white/20 flex items-center justify-center font-bold text-xl">
                        {{ substr($selectedStudent->user->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">{{ $selectedStudent->user->name }}</h2>
                        <p class="text-blue-200 text-sm">{{ $selectedStudent->nim }} • {{ $selectedStudent->study_program->name ?? '-' }}</p>
                    </div>
                </div>
                <button wire:click="resetStudent" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-medium transition-colors">
                    Ganti Mahasiswa
                </button>
            </div>
        @endif
    </div>

    @if($selectedStudent)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">Tambah Mata Kuliah</h3>
                <input wire:model.live.debounce.500ms="search_class" type="text" placeholder="Filter Matkul..." class="text-sm rounded-lg border-slate-300 w-48 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="max-h-[500px] overflow-y-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 sticky top-0">
                            <tr>
                                <th class="px-4 py-3">Matkul</th>
                                <th class="px-4 py-3 text-center">Kelas</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($available_classes as $class)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $class->course->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $class->course->code }} • {{ $class->course->credit_total }} SKS</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-slate-100 px-2 py-1 rounded text-xs font-bold dark:bg-slate-600 dark:text-white">{{ $class->name }}</span>
                                    <div class="text-[10px] text-slate-400 mt-1">Sisa: {{ $class->quota - $class->enrolled }}</div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button wire:click="addClass('{{ $class->id }}')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition-colors">
                                        + Tambahkan
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">Tidak ada kelas tersedia / sesuai filter.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-lg text-slate-800 dark:text-white">KRS Saat Ini</h3>
                <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold dark:bg-indigo-900/30 dark:text-indigo-400">
                    Total: {{ $taken_classes->sum(fn($k) => $k->classroom->course->credit_total) }} SKS
                </span>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-900 dark:text-indigo-300 sticky top-0">
                        <tr>
                            <th class="px-4 py-3">Mata Kuliah</th>
                            <th class="px-4 py-3 text-center">SKS</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($taken_classes as $plan)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $plan->classroom->course->name }}</div>
                                <div class="text-xs text-slate-500">
                                    Kelas {{ $plan->classroom->name }} 
                                    @if($plan->status == 'APPROVED') 
                                        <span class="text-green-600 font-bold ml-1">✓ Disetujui</span>
                                    @else
                                        <span class="text-yellow-600 font-bold ml-1">⏱ {{ $plan->status }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-slate-600 dark:text-slate-400">
                                {{ $plan->classroom->course->credit_total }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="removeClass('{{ $plan->id }}')" wire:confirm="Hapus matkul ini dari mahasiswa?" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors dark:bg-red-900/20 dark:hover:bg-red-900/40">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="px-4 py-8 text-center text-slate-400">Mahasiswa ini belum mengambil KRS.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @endif
</div>