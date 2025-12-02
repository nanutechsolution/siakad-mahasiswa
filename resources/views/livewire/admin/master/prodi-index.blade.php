<div>
    <x-slot name="header">Master Program Studi</x-slot>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Prodi / Kaprodi..."
            class="rounded-lg border-slate-300 w-full md:w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
        
        <button wire:click="create"
            class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-500/30">
            + Tambah Prodi
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Fakultas</th>
                    <th class="px-6 py-4">Jenjang</th>
                    <th class="px-6 py-4">Nama Program Studi</th>
                    <th class="px-6 py-4">Kaprodi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($prodis as $p)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-brand-blue dark:text-brand-gold">{{ $p->code }}</td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-300">{{ $p->faculty->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold dark:bg-blue-900 dark:text-blue-300">
                                {{ $p->degree }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">{{ $p->name }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $p->head_name ?? '-' }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $p->head_nip }}</div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <!-- TOMBOL RIWAYAT BARU -->
                            <button wire:click="showHistory({{ $p->id }})" class="text-slate-500 hover:text-slate-800 text-xs font-medium dark:text-slate-400 dark:hover:text-white mr-2" title="Lihat Riwayat Pejabat">
                                📜 Riwayat
                            </button>
                            <button wire:click="edit({{ $p->id }})" class="text-blue-600 hover:underline font-medium dark:text-blue-400">Edit</button>
                            <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus Prodi ini?" class="text-red-600 hover:underline font-medium dark:text-red-400">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                            Belum ada data program studi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">
            {{ $prodis->links() }}
        </div>
    </div>

    <!-- Modal Form (Create/Edit) -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                        {{ $isEditMode ? 'Edit Prodi' : 'Tambah Prodi Baru' }}
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Fakultas Induk</label>
                        <select wire:model="faculty_id" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach(\App\Models\Faculty::all() as $f)
                                <option value="{{ $f->id }}">{{ $f->name }} ({{ $f->code }})</option>
                            @endforeach
                        </select>
                        @error('faculty_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Kode</label>
                            <input wire:model="code" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue uppercase dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="TI">
                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Jenjang</label>
                            <select wire:model="degree" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                <option value="S1">Sarjana (S1)</option>
                                <option value="D3">Diploma (D3)</option>
                                <option value="S2">Magister (S2)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Nama Program Studi</label>
                        <input wire:model="name" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Teknik Informatika">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Pejabat Prodi (Grouped) -->
                    <div class="pt-4 mt-2 border-t border-slate-100 dark:border-slate-700">
                        <label class="block text-xs font-bold text-brand-blue mb-3 uppercase dark:text-brand-gold">Pejabat Prodi</label>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-500 dark:text-slate-400">Nama Kaprodi</label>
                                <input wire:model="head_name" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Nama Lengkap & Gelar">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-500 dark:text-slate-400">NIP / NIDN</label>
                                <input wire:model="head_nip" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-6 pt-2 bg-slate-50 dark:bg-slate-800 dark:border-t dark:border-slate-700">
                    <button wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-lg text-slate-500 hover:bg-slate-200 font-medium dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                    <button wire:click="store" class="px-6 py-2 rounded-lg bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-md shadow-blue-500/20">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL RIWAYAT KAPRODI (BARU) -->
    @if($isHistoryOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg p-6 relative">
            
            <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-slate-700">
                <h3 class="font-bold text-lg dark:text-white">Riwayat Kaprodi</h3>
                <button wire:click="$set('isHistoryOpen', false)" class="text-slate-400 hover:text-red-500 font-bold text-xl">&times;</button>
            </div>

            <div class="overflow-y-auto max-h-96 pr-2 custom-scrollbar">
                @if(count($historyList) > 0)
                    <ol class="relative border-l border-slate-200 dark:border-slate-700 ml-3">                  
                        @foreach($historyList as $his)
                        <li class="mb-6 ml-4">
                            <div class="absolute w-3 h-3 bg-slate-200 rounded-full mt-1.5 -left-1.5 border border-white dark:border-slate-900 dark:bg-slate-700"></div>
                            <time class="mb-1 text-xs font-normal leading-none text-slate-400">
                                {{ $his->start_date->format('d M Y') }} 
                                s/d 
                                {{ $his->end_date ? $his->end_date->format('d M Y') : 'Sekarang' }}
                            </time>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">
                                {{ $his->official_name }}
                            </h3>
                            <p class="text-xs text-slate-500">{{ $his->position }} {{ $his->official_nip ? '('.$his->official_nip.')' : '' }}</p>
                            @if($his->is_active)
                                <span class="bg-green-100 text-green-800 text-[10px] font-medium px-2 py-0.5 rounded dark:bg-green-900/30 dark:text-green-300 mt-1 inline-block">Menjabat</span>
                            @else
                                <span class="bg-slate-100 text-slate-500 text-[10px] font-medium px-2 py-0.5 rounded dark:bg-slate-700 dark:text-slate-400 mt-1 inline-block">Selesai</span>
                            @endif
                        </li>
                        @endforeach
                    </ol>
                @else
                    <div class="text-center py-8 text-slate-400 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-dashed border-slate-200 dark:border-slate-600">
                        <p class="text-sm">Belum ada riwayat Kaprodi tercatat.</p>
                        <p class="text-xs mt-1">Riwayat akan muncul otomatis saat Anda mengganti nama Kaprodi.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>