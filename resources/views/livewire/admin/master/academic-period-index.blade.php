<div>
    <x-slot name="header">Master Periode Akademik</x-slot>

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

    <div class="flex justify-between mb-6">
        <input wire:model.live="search" type="text" placeholder="Cari Tahun / Semester..." class="rounded-lg border-slate-300 w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
        <button wire:click="create" class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition">
            + Periode Baru
        </button>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Nama Periode</th>
                    <th class="px-6 py-4">Tanggal Mulai - Selesai</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($periods as $p)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <td class="px-6 py-4 font-mono font-bold text-brand-blue dark:text-brand-gold">{{ $p->code }}</td>
                    <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">{{ $p->name }}</td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $p->start_date ? $p->start_date->format('d M Y') : '-' }} s/d 
                        {{ $p->end_date ? $p->end_date->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($p->is_active)
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-bold text-green-700">
                                <span class="h-2 w-2 rounded-full bg-green-500"></span> Aktif
                            </span>
                        @else
                            <span class="text-xs text-slate-400">Arsip</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button wire:click="edit({{ $p->id }})" class="text-blue-600 hover:underline font-medium">Edit</button>
                        @if(!$p->is_active)
                            <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus periode ini?" class="text-red-600 hover:underline font-medium">Hapus</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $periods->links() }}</div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4 text-slate-900 dark:text-white">
                {{ $isEditMode ? 'Edit Periode' : 'Buat Periode Baru' }}
            </h3>
            
            <form wire:submit.prevent="store" class="space-y-4">
                
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-slate-300">Kode Periode</label>
                    <input wire:model="code" type="number" class="w-full rounded border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Contoh: 20241">
                    <p class="text-[10px] text-slate-400 mt-1">Gunakan format TAHUN + 1 (Ganjil) / 2 (Genap). Cth: 20241</p>
                    @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-slate-300">Nama Periode</label>
                    <input wire:model="name" type="text" class="w-full rounded border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Ganjil 2024/2025">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-slate-300">Tanggal Mulai</label>
                        <input wire:model="start_date" type="date" class="w-full rounded border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-slate-300">Tanggal Selesai</label>
                        <input wire:model="end_date" type="date" class="w-full rounded border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-lg shadow-blue-500/30">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>