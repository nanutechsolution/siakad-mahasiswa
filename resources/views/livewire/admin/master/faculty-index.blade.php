<div>
    <x-slot name="header">Master Data Fakultas</x-slot>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Fakultas / Dekan..."
            class="rounded-lg border-slate-300 w-full md:w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">

        <button wire:click="create"
            class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-500/30">
            + Tambah Fakultas
        </button>
    </div>

    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4 w-20">Kode</th>
                    <th class="px-6 py-4">Nama Fakultas</th>
                    <th class="px-6 py-4">Dekan</th>
                    <th class="px-6 py-4 text-center">Jml Prodi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($faculties as $f)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-brand-blue dark:text-brand-gold">
                            {{ $f->code }}
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 dark:text-white">
                            {{ $f->name }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                            {{ $f->dean_name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold dark:bg-slate-600 dark:text-slate-200">
                                {{ $f->study_programs()->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button wire:click="edit({{ $f->id }})"
                                class="text-blue-600 hover:underline font-medium dark:text-blue-400">Edit</button>
                            <button wire:click="delete({{ $f->id }})"
                                wire:confirm="Hapus Fakultas ini? Pastikan tidak ada Prodi yang terkait."
                                class="text-red-600 hover:underline font-medium dark:text-red-400">Hapus</button>
                            <button wire:click="showHistory({{ $f->id }})"
                                class="text-slate-500 hover:text-slate-800 text-xs">
                                📜 Riwayat
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            Belum ada data fakultas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">
            {{ $faculties->links() }}
        </div>
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">

                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                        {{ $isEditMode ? 'Edit Fakultas' : 'Tambah Fakultas Baru' }}
                    </h3>
                </div>

                <form wire:submit.prevent="store" class="p-6 space-y-4">

                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-slate-300">Kode Fakultas</label>
                        <input wire:model="code" type="text"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white uppercase"
                            placeholder="FT">
                        @error('code')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-slate-300">Nama Fakultas</label>
                        <input wire:model="name" type="text"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            placeholder="Fakultas Teknik">
                        @error('name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-slate-300">Nama Dekan (Opsional)</label>
                        <input wire:model="dean_name" type="text"
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            placeholder="Dr. ...">
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('isModalOpen', false)"
                            class="px-4 py-2 rounded-lg text-slate-500 hover:bg-slate-100 font-medium dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-md shadow-blue-500/20">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif


    @if ($isHistoryOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg dark:text-white">Riwayat Pejabat</h3>
                    <button wire:click="$set('isHistoryOpen', false)">✕</button>
                </div>

                <div class="overflow-y-auto max-h-96">
                    <ol class="relative border-l border-slate-200 dark:border-slate-700 ml-3">
                        @foreach ($historyList as $his)
                            <li class="mb-6 ml-4">
                                <div
                                    class="absolute w-3 h-3 bg-slate-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-slate-900 dark:bg-slate-700">
                                </div>
                                <time class="mb-1 text-xs font-normal leading-none text-slate-400">
                                    {{ $his->start_date->format('d M Y') }}
                                    s/d
                                    {{ $his->end_date ? $his->end_date->format('d M Y') : 'Sekarang' }}
                                </time>
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                                    {{ $his->official_name }}
                                </h3>
                                <p class="text-xs text-slate-500">{{ $his->position }}
                                    {{ $his->official_nip ? '(' . $his->official_nip . ')' : '' }}</p>
                                @if ($his->is_active)
                                    <span
                                        class="bg-green-100 text-green-800 text-[10px] font-medium px-2 py-0.5 rounded dark:bg-green-900 dark:text-green-300">Menjabat</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                    @if (count($historyList) == 0)
                        <p class="text-center text-slate-500 text-sm">Belum ada riwayat tercatat.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
