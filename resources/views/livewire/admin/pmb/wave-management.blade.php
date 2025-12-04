<div>
    <x-slot name="header">Pengaturan Gelombang PMB</x-slot>

    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200">
            ✅ {{ session('message') }}
        </div>
    @endif

    <div class="flex justify-end mb-6">
        <button wire:click="create" class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow-lg">
            + Buat Gelombang Baru
        </button>
    </div>

    <!-- Grid Gelombang -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($waves as $wave)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 relative overflow-hidden">
            
            <!-- Status Badge -->
            <div class="absolute top-4 right-4">
                <button wire:click="toggleActive({{ $wave->id }})" 
                        class="px-3 py-1 rounded-full text-[10px] font-bold uppercase transition-colors {{ $wave->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                    {{ $wave->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                </button>
            </div>

            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">{{ $wave->name }}</h3>
            
            <!-- Tanggal -->
            <div class="flex items-center gap-2 text-sm text-slate-500 mt-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                <span>{{ $wave->start_date->format('d M Y') }}</span>
                <span>-</span>
                <span>{{ $wave->end_date->format('d M Y') }}</span>
            </div>

            <!-- Status Waktu -->
            <div class="mt-3">
                @if(now()->between($wave->start_date, $wave->end_date) && $wave->is_active)
                    <span class="text-xs font-bold text-green-600 flex items-center gap-1">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                        </span>
                        Sedang Berlangsung
                    </span>
                @elseif(now()->lt($wave->start_date))
                    <span class="text-xs font-bold text-yellow-600">Segera Dibuka</span>
                @else
                    <span class="text-xs font-bold text-red-500">Sudah Berakhir</span>
                @endif
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                <button wire:click="edit({{ $wave->id }})" class="text-xs font-bold text-blue-600 hover:underline">Edit</button>
                <button wire:confirm="Hapus gelombang ini?" wire:click="delete({{ $wave->id }})" class="text-xs font-bold text-red-600 hover:underline">Hapus</button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">{{ $isEditMode ? 'Edit Gelombang' : 'Buat Gelombang Baru' }}</h3>
            
            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-slate-300">Nama Gelombang</label>
                    <input wire:model="name" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white" placeholder="Contoh: Gelombang 1 - Reguler">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-slate-300">Mulai</label>
                        <input wire:model="start_date" type="date" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 dark:text-slate-300">Selesai</label>
                        <input wire:model="end_date" type="date" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white">
                    </div>
                </div>
                
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" wire:model="is_active" class="rounded text-brand-blue focus:ring-brand-blue">
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Aktifkan Gelombang Ini</span>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-slate-100 dark:border-slate-700 mt-4">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded text-slate-500 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded bg-brand-blue text-white font-bold hover:bg-blue-800">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>