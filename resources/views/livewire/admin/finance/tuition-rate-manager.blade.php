<div class="space-y-6">
    <x-slot name="header">Master Tarif SPP & Biaya</x-slot>

    <!-- Alert -->
    @if (session()->has('message'))
        <div
            class="p-4 bg-green-100 text-green-700 rounded-xl font-bold border border-green-200 shadow-sm flex items-center gap-2">
            ✅ {{ session('message') }}
        </div>
    @endif

    <!-- Toolbar -->
    <div
        class="flex flex-col md:flex-row gap-4 justify-between items-center bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex gap-2 w-full md:w-auto">
            <select wire:model.live="filter_prodi"
                class="rounded-xl border-slate-300 text-sm dark:bg-slate-900 dark:border-slate-700 dark:text-white">
                <option value="">Semua Prodi</option>
                @foreach ($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Tahun..."
                class="rounded-xl border-slate-300 w-32 dark:bg-slate-900 dark:border-slate-700 dark:text-white">
        </div>

        <button wire:click="create"
            class="bg-brand-blue text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-blue-800 transition shadow-lg shadow-blue-900/20">
            + Tambah Tarif
        </button>
    </div>

    <!-- Tabel -->
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead
                class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-xs font-bold border-b border-slate-100 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-4">Program Studi</th>
                    <th class="px-6 py-4 text-center">Angkatan</th>
                    <th class="px-6 py-4">Jenis Biaya</th>
                    <th class="px-6 py-4 text-right">Nominal</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($rates as $rate)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">
                            {{ $rate->study_program->name }}
                            <span
                                class="text-xs text-slate-400 block font-normal">{{ $rate->study_program->degree }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded-md font-bold text-xs border border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">
                                {{ $rate->entry_year }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                            {{ $rate->fee_type->name ?? '-' }} <span
                                class="text-xs text-slate-400">({{ $rate->fee_type->code ?? 'N/A' }})</span>
                        </td>
                        <td class="px-6 py-4 text-right font-mono font-black text-slate-900 dark:text-white">
                            Rp {{ number_format($rate->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="edit('{{ $rate->id }}')"
                                class="text-blue-600 hover:text-blue-800 font-bold text-xs mr-2">Edit</button>
                            <button wire:click="delete('{{ $rate->id }}')" wire:confirm="Hapus tarif ini?"
                                class="text-red-500 hover:text-red-700 font-bold text-xs">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada data tarif.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-50 dark:border-slate-700">{{ $rates->links() }}</div>
    </div>

    <!-- MODAL FORM -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">
                    {{ $isEditMode ? 'Edit Tarif' : 'Tambah Tarif Baru' }}</h3>

                <form wire:submit.prevent="store" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Program
                            Studi</label>
                        <select wire:model="study_program_id"
                            class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                            <option value="">-- Pilih Prodi --</option>
                            @foreach ($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->degree }})
                                </option>
                            @endforeach
                        </select>
                        @error('study_program_id')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Angkatan</label>
                            <input wire:model="entry_year" type="number"
                                class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white"
                                placeholder="2024">
                            @error('entry_year')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Jenis
                                Biaya</label>
                            <select wire:model="fee_type_id"
                                class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                @foreach ($fee_types as $ft)
                                    <option value="{{ $ft->id }}">{{ $ft->name }} ({{ $ft->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Nominal
                            (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-400 font-bold text-sm">Rp</span>
                            <input wire:model="amount" type="number"
                                class="w-full pl-10 rounded-lg border-slate-300 font-mono font-bold dark:bg-slate-900 dark:border-slate-600 dark:text-white"
                                placeholder="0">
                        </div>
                        @error('amount')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t dark:border-slate-700 mt-2">
                        <button type="button" wire:click="$set('isModalOpen', false)"
                            class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 rounded-lg bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
