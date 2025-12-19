<div class="space-y-6 font-sans">
    <x-slot name="header">Master Data Dosen</x-slot>

    <!-- Alert Notifikasi -->
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-2 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Toolbar: Pencarian & Filter -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto flex-1">
            <!-- Search -->
            <div class="relative flex-1 w-full">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / NIDN / NIP..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all shadow-sm">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            
            <!-- Filter Prodi -->
            <select wire:model.live="filter_prodi" class="w-full sm:w-auto rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue shadow-sm cursor-pointer">
                <option value="">Semua Homebase</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2 w-full sm:w-auto">
            <button wire:click="openImportModal" class="flex-1 sm:flex-none px-4 py-2.5 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-700 dark:text-white rounded-xl font-bold transition shadow-sm flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                Import
            </button>
            <button wire:click="create" class="flex-1 sm:flex-none px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2 transform active:scale-95">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah
            </button>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Dosen</th>
                        <th class="px-6 py-4">Identitas (NIDN/NIP)</th>
                        <th class="px-6 py-4">Homebase</th>
                        <th class="px-6 py-4">Kontak</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($lecturers as $l)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-sm font-bold text-slate-500 dark:text-slate-300">
                                    {{ substr($l->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $l->front_title }} {{ $l->user->name }} {{ $l->back_title }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $l->user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                @if($l->nidn)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded border border-slate-200 bg-slate-50 text-slate-600 text-[10px] font-mono">
                                        <span class="font-bold">NIDN:</span> {{ $l->nidn }}
                                    </span>
                                @endif
                                @if($l->nip_internal)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded border border-slate-200 bg-white text-slate-500 text-[10px] font-mono">
                                        <span class="font-bold">NIP:</span> {{ $l->nip_internal }}
                                    </span>
                                @endif
                                @if(!$l->nidn && !$l->nip_internal)
                                    <span class="text-xs text-slate-300">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($l->study_program)
                                <span class="px-2 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100 text-xs font-bold dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">
                                    {{ $l->study_program->name }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs italic">Umum / Tidak ada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 text-xs">
                            @if($l->phone)
                                <div class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                    {{ $l->phone }}
                                </div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1">
                                <button wire:click="edit('{{ $l->id }}')" class="p-2 text-slate-500 hover:text-brand-blue hover:bg-blue-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button wire:click="delete('{{ $l->id }}')" wire:confirm="Yakin ingin menghapus dosen ini? User terkait juga akan dihapus." class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data dosen ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700">
            {{ $lecturers->links() }}
        </div>
    </div>

    <!-- MODAL IMPORT (NEW) -->
    @if($isImportModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    Import Data Dosen
                </h3>
                <p class="text-xs text-slate-500 mt-1">Upload file CSV untuk menambahkan dosen secara massal.</p>
            </div>
            
            <form wire:submit.prevent="import" class="p-6 space-y-4">
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800 leading-relaxed">
                    <strong>Format CSV:</strong> <br>
                    <code>Nama, Email, NIDN, NIP, Kode Prodi</code><br>
                    <span class="text-blue-500 italic mt-1 block">*Baris pertama (header) akan diabaikan.</span>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Pilih File CSV</label>
                    <input type="file" wire:model="file_import" accept=".csv, .txt" 
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-brand-blue file:text-white hover:file:bg-blue-700 transition cursor-pointer">
                    @error('file_import') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" wire:click="closeImportModal" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-lg transition">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" class="px-6 py-2 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-700 shadow-lg shadow-green-600/20 transition flex items-center gap-2">
                        <span wire:loading.remove>Upload & Import</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL CREATE/EDIT (RESPONSIVE) -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl w-full max-w-2xl mx-auto transform transition-all scale-100 overflow-hidden flex flex-col max-h-[90vh] sm:max-h-auto">
            
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/50 flex-shrink-0 flex justify-between items-center">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">{{ $isEditMode ? 'Edit Data Dosen' : 'Registrasi Dosen Baru' }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Lengkapi profil dan data akademik dosen.</p>
                </div>
                <button wire:click="closeModal" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="overflow-y-auto p-6 sm:p-8 space-y-6">
                <form wire:submit.prevent="store" id="lecturerForm">
                    
                    <!-- Section: Identitas -->
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
                            <span class="w-6 h-[1px] bg-slate-300"></span> Identitas Diri
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Gelar Depan</label>
                                <input wire:model="front_title" type="text" placeholder="Dr." class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                            </div>
                            <div class="sm:col-span-6">
                                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Nama Lengkap (Tanpa Gelar)</label>
                                <input wire:model="name" type="text" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Gelar Blkg</label>
                                <input wire:model="back_title" type="text" placeholder="S.Kom., M.Kom." class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Akademik -->
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2 mt-2">
                            <span class="w-6 h-[1px] bg-slate-300"></span> Data Akademik
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">NIDN (Nasional)</label>
                                <input wire:model="nidn" type="text" class="w-full rounded-xl border-slate-300 text-sm font-mono font-bold focus:ring-brand-blue">
                                @error('nidn') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">NIP Internal</label>
                                <input wire:model="nip_internal" type="text" class="w-full rounded-xl border-slate-300 text-sm font-mono font-bold focus:ring-brand-blue">
                                @error('nip_internal') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Homebase Prodi</label>
                                <select wire:model="study_program_id" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                    <option value="">-- Pilih Homebase --</option>
                                    @foreach($prodis as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('study_program_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">No. WhatsApp</label>
                                <input wire:model="phone" type="text" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Akun -->
                    <div class="bg-slate-50 dark:bg-slate-700/30 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Email Login</label>
                                <input wire:model="email" type="email" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                @error('email') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Password</label>
                                <input wire:model="password" type="password" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue" 
                                    placeholder="{{ $isEditMode ? 'Kosongkan jika tetap' : 'Min 6 karakter' }}">
                                @error('password') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Footer Action -->
            <div class="px-6 sm:px-8 py-5 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3 flex-shrink-0 bg-white dark:bg-slate-800">
                <button type="button" wire:click="closeModal" class="px-6 py-2.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition text-sm">Batal</button>
                <button type="submit" form="lecturerForm" wire:loading.attr="disabled" wire:target="store" 
                    class="px-8 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 shadow-lg transition flex items-center gap-2 disabled:opacity-50 text-sm">
                    <span wire:loading.remove wire:target="store">Simpan Data</span>
                    <span wire:loading wire:target="store">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>