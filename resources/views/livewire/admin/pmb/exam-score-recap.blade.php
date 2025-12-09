<div>
    <x-slot name="header">Rekap Hasil Ujian & Ranking</x-slot>

    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200 shadow-sm flex items-center gap-2">
            ✅ {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 font-bold border border-red-200 shadow-sm flex items-center gap-2">
            ❌ {{ session('error') }}
        </div>
    @endif

    <!-- FILTER BAR -->
    <div class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <div class="flex flex-col md:flex-row gap-4 justify-between items-end">
            <div class="flex flex-col md:flex-row gap-4 w-full">
                <div class="w-full md:w-64">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Filter Prodi</label>
                    <select wire:model.live="filter_prodi" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white text-sm">
                        <option value="">Semua Prodi</option>
                        @foreach($prodis as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full md:w-40">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Min. Skor</label>
                    <input wire:model.live.debounce.500ms="min_score" type="number" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white text-sm" placeholder="0">
                </div>

                <div class="w-full md:w-64">
                    <label class="block text-xs font-bold text-slate-500 mb-1">Cari Peserta</label>
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-9 rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white text-sm" placeholder="Nama / No. Daftar...">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                </div>
            </div>

            <button wire:click="export" class="shrink-0 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                Export Excel
            </button>
        </div>
    </div>

    <!-- LEADERBOARD TABLE -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-100 dark:bg-slate-900/50 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4 w-16 text-center">Rank</th>
                    <th class="px-6 py-4">Peserta</th>
                    <th class="px-6 py-4">Prodi Pilihan</th>
                    <th class="px-6 py-4 text-center">Skor Ujian</th>
                    <th class="px-6 py-4 text-center">Status Kelulusan</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($results as $res)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex h-8 w-8 items-center justify-center rounded-full font-bold {{ $loop->iteration <= 3 ? 'bg-yellow-100 text-yellow-700' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300' }}">
                            {{ $loop->iteration + ($results->currentPage() - 1) * $results->perPage() }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $res->registrant->user->name }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $res->registrant->registration_no }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold border border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                            {{ $res->registrant->firstChoice->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="text-xl font-black {{ $res->total_score >= 80 ? 'text-green-600' : ($res->total_score < 50 ? 'text-red-500' : 'text-slate-700 dark:text-slate-200') }}">
                            {{ $res->total_score }}
                        </div>
                        <div class="text-[10px] text-slate-400">
                            Durasi: {{ $res->finished_at->diffInMinutes($res->started_at) }} Menit
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                         <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase border
                            {{ match($res->registrant->status->value) {
                                'ACCEPTED' => 'bg-green-100 text-green-700 border-green-200',
                                'REJECTED' => 'bg-red-100 text-red-700 border-red-200',
                                default => 'bg-slate-100 text-slate-600 border-slate-200',
                            } }}">
                            {{ $res->registrant->status->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($res->registrant->status->value != 'ACCEPTED' && $res->registrant->status->value != 'REJECTED')
                            <div class="flex justify-end gap-2">
                                <button wire:click="passRegistrant('{{ $res->registrant->id }}')" wire:confirm="Nyatakan LULUS?" class="p-1.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 border border-green-200" title="Luluskan">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                </button>
                                <button wire:click="failRegistrant('{{ $res->registrant->id }}')" wire:confirm="Nyatakan TIDAK LULUS?" class="p-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 border border-red-200" title="Gagalkan">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">Sudah Diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 border-dashed border-2 border-slate-200 rounded-xl m-4">
                        Belum ada peserta yang menyelesaikan ujian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700">
            {{ $results->links() }}
        </div>
    </div>
</div>