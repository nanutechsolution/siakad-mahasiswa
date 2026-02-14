<div class="space-y-6 font-sans">
    <x-slot name="header">Kelompok Matakuliah</x-slot>

    <!-- Notifikasi -->
    @if (session()->has('message'))
    <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-2 shadow-sm animate-fade-in-down">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('message') }}
    </div>
    @endif

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="relative w-full sm:w-96">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Kode atau Nama..."
                class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all shadow-sm">
            <div class="absolute left-3 top-2.5 text-slate-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <button wire:click="create" class="w-full sm:w-auto px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Group
        </button>
    </div>

    <!-- Tabel -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 w-20">Kode</th>
                        <th class="px-6 py-4">Nama Kelompok</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($groups as $group)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 font-black text-brand-blue">
                            <span class="bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded text-xs">{{ $group->code }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-700 dark:text-white">{{ $group->name }}</td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $group->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="edit('{{ $group->id }}')" class="p-2 text-slate-400 hover:text-brand-blue transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button wire:click="delete('{{ $group->id }}')" 
                                        wire:confirm="Yakin ingin menghapus group ini?"
                                        class="p-2 text-slate-400 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">Belum ada data course group.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 border-t border-slate-100 dark:border-slate-700">
            {{ $groups->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 flex justify-between items-center">
                <h3 class="font-bold text-lg dark:text-white">{{ $isEditMode ? 'Edit Group' : 'Tambah Group' }}</h3>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <form wire:submit.prevent="save" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kode <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="code" placeholder="Contoh: MKK" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue uppercase">
                    @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kelompok <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name" placeholder="Contoh: Matakuliah Keilmuan" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                    @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Keterangan</label>
                    <textarea wire:model="description" rows="3" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue"></textarea>
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" wire:click="closeModal" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg font-bold transition">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-brand-blue text-white rounded-lg font-bold hover:bg-blue-800 transition shadow-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>