<div class="space-y-6 font-sans">
    <x-slot name="header">Master Jenis Biaya</x-slot>

    <!-- Alert -->
    @if (session()->has('message'))
        <div class="p-4 bg-green-100 text-green-700 rounded-xl font-bold border border-green-200 shadow-sm flex items-center gap-2 animate-fade-in-up">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="w-full md:w-96 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Jenis Biaya..." class="block w-full pl-10 rounded-xl border-slate-200 bg-slate-50 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue focus:border-brand-blue">
        </div>

        <button wire:click="create" class="flex items-center gap-2 px-5 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Jenis Biaya
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest border-b border-slate-100 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Nama Biaya</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($types as $type)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-6 py-4 font-mono font-bold text-brand-blue dark:text-brand-gold">
                        {{ $type->code }}
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">
                        {{ $type->name }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($type->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                AKTIF
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                NON-AKTIF
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button wire:click="edit({{ $type->id }})" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </button>
                            <button wire:click="delete({{ $type->id }})" wire:confirm="Hapus jenis biaya ini? Tagihan lama dengan jenis ini mungkin terpengaruh." class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-500 italic">
                        Belum ada jenis biaya. Silakan tambah baru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-50 dark:border-slate-700">
            {{ $types->links() }}
        </div>
    </div>

    <!-- MODAL FORM -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-100">
            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-700/50">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                    {{ $isEditMode ? 'Edit Jenis Biaya' : 'Tambah Jenis Biaya' }}
                </h3>
                <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <form wire:submit.prevent="store" class="p-8 space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Unik</label>
                    <input wire:model="code" type="text" placeholder="Contoh: SPP, GEDUNG, KKN" class="w-full rounded-xl border-slate-300 uppercase font-mono font-bold dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                    <p class="text-[10px] text-slate-500 mt-1">Gunakan huruf kapital, tanpa spasi.</p>
                    @error('code') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Biaya</label>
                    <input wire:model="name" type="text" placeholder="Contoh: Sumbangan Pembinaan Pendidikan" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                    @error('name') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model="is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"></div>
                        <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300">Aktifkan</span>
                    </label>
                </div>

                <div class="pt-6 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-6 py-2.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Batal</button>
                    <button type="submit" class="px-8 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 shadow-lg shadow-blue-900/20 transition flex items-center gap-2">
                        <span>Simpan</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>