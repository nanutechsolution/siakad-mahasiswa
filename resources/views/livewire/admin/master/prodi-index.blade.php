<div>
    <x-slot name="header">Master Program Studi</x-slot>

    <!-- ... Alert & Search (Sama) ... -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Prodi / Kaprodi..."
            class="rounded-lg border-slate-300 w-full md:w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
        <button wire:click="create" class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-500/30">
            + Tambah Prodi
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Jenjang</th>
                    <th class="px-6 py-4">Nama Program Studi</th>
                    <th class="px-6 py-4 text-center">Kurikulum</th> <!-- Kolom Baru -->
                    <th class="px-6 py-4">Kaprodi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse ($prodis as $p)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-brand-blue dark:text-brand-gold">{{ $p->code }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold dark:bg-blue-900 dark:text-blue-300">{{ $p->degree }}</span>
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">
                            {{ $p->name }}
                            <div class="text-xs text-slate-500 font-normal">{{ $p->faculty->name ?? '-' }}</div>
                        </td>
                        
                        <!-- BADGE SYSTEM PAKET -->
                        <td class="px-6 py-4 text-center">
                            @if($p->is_package)
                                <span class="inline-flex items-center gap-1 bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold border border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                                    Paket Penuh
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold border border-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600">
                                    SKS Mandiri
                                </span>
                            @endif
                            <div class="text-[10px] text-slate-400 mt-1">Target: {{ $p->total_credits }} SKS</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $p->head_name ?? '-' }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $p->head_nip }}</div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button wire:click="showHistory({{ $p->id }})" class="text-slate-500 hover:text-slate-800 text-xs font-medium dark:text-slate-400 dark:hover:text-white mr-2">📜 Riwayat</button>
                            <button wire:click="edit({{ $p->id }})" class="text-blue-600 hover:underline font-medium dark:text-blue-400">Edit</button>
                            <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus Prodi ini?" class="text-red-600 hover:underline font-medium dark:text-red-400">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada data program studi.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">
            {{ $prodis->links() }}
        </div>
    </div>

    <!-- Modal Form -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden my-8">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $isEditMode ? 'Edit Prodi' : 'Tambah Prodi Baru' }}</h3>
                </div>

                <div class="p-6 space-y-4">
                    <!-- Fakultas -->
                    <div>
                        <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Fakultas Induk</label>
                        <select wire:model="faculty_id" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
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
                            <input wire:model="code" type="text" class="w-full rounded-lg border-slate-300 text-sm uppercase dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="TI">
                            @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Jenjang</label>
                            <select wire:model="degree" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                <option value="S1">Sarjana (S1)</option>
                                <option value="D3">Diploma (D3)</option>
                                <option value="S2">Magister (S2)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Nama Program Studi</label>
                        <input wire:model="name" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Teknik Informatika">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- TOGGLE SISTEM PAKET (BARU) -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg border border-slate-100 dark:border-slate-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-sm font-bold text-slate-800 dark:text-white">Sistem Paket Penuh?</label>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Jika aktif, KRS mahasiswa akan otomatis diambilkan.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="is_package" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-purple-600"></div>
                            </label>
                        </div>
                        <div class="mt-3">
                            <label class="block text-xs font-bold text-slate-500 mb-1">Target Total SKS Lulus</label>
                            <input wire:model="total_credits" type="number" class="w-20 text-center rounded-lg border-slate-300 text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                        </div>
                    </div>

                    <!-- Pejabat Prodi -->
                    <div class="pt-4 mt-2 border-t border-slate-100 dark:border-slate-700">
                        <label class="block text-xs font-bold text-brand-blue mb-3 uppercase dark:text-brand-gold">Pejabat Prodi</label>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold mb-1 text-slate-500 dark:text-slate-400">Nama Kaprodi</label>
                                <input wire:model="head_name" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Nama Lengkap & Gelar">
                            </div>
                            
                            <!-- TANGGAL MENJABAT (UTK RIWAYAT) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-500 dark:text-slate-400">NIP / NIDN</label>
                                    <input wire:model="head_nip" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-500 dark:text-slate-400">Tgl SK Menjabat</label>
                                    <input wire:model="head_start_date" type="date" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                </div>
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
    
    <!-- ... (Modal Riwayat sama seperti sebelumnya) ... -->
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