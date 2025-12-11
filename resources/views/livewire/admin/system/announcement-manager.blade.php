<div>
    <x-slot name="header">Kelola Pengumuman</x-slot>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 text-green-700 p-4 rounded-lg font-bold">{{ session('message') }}</div>
    @endif

    <div class="flex justify-between mb-6">
        <input wire:model.live="search" type="text" placeholder="Cari Judul..." class="rounded-lg border-slate-300 w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
        <button wire:click="create" class="bg-brand-blue text-white px-4 py-2 rounded-lg font-bold shadow-lg">+ Buat Pengumuman</button>
    </div>

    <div class="grid gap-4">
        @foreach($announcements as $ann)
        <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-start">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-1 rounded text-xs font-bold uppercase {{ $ann->target_role == 'all' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $ann->target_role == 'all' ? 'Semua User' : ($ann->target_role == 'student' ? 'Mahasiswa' : 'Dosen') }}
                    </span>
                    <span class="text-xs text-slate-400">{{ $ann->created_at->format('d M Y H:i') }}</span>
                    @if(!$ann->is_active) <span class="bg-red-100 text-red-600 px-2 py-0.5 rounded text-[10px]">Non-Aktif</span> @endif
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $ann->title }}</h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mt-1 line-clamp-2">{{ $ann->content }}</p>
                @if($ann->attachment)
                    <a href="{{ asset('storage/'.$ann->attachment) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-500 mt-2 hover:underline">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                        Lampiran
                    </a>
                @endif
            </div>
            <div class="flex gap-2">
                <button wire:click="edit({{ $ann->id }})" class="p-2 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200"><svg class="w-4 h-4 text-slate-600 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                <button wire:click="delete({{ $ann->id }})" wire:confirm="Hapus?" class="p-2 bg-red-100 rounded-lg hover:bg-red-200"><svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg></button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- MODAL -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-4">Form Pengumuman</h3>
            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="text-sm font-bold dark:text-slate-300">Judul</label>
                    <input wire:model="title" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white">
                </div>
                <div>
                    <label class="text-sm font-bold dark:text-slate-300">Target</label>
                    <select wire:model="target_role" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white">
                        <option value="all">Semua Pengguna</option>
                        <option value="student">Hanya Mahasiswa</option>
                        <option value="lecturer">Hanya Dosen</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-bold dark:text-slate-300">Isi Pengumuman</label>
                    <textarea wire:model="content" rows="4" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="text-sm font-bold dark:text-slate-300">Lampiran (Opsional)</label>
                    <input wire:model="attachment" type="file" class="w-full text-sm text-slate-500">
                </div>
                <div class="flex gap-2">
                    <input type="checkbox" wire:model="is_active"> <span class="text-sm dark:text-slate-300">Langsung Terbitkan</span>
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 bg-slate-100 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-blue text-white rounded font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>