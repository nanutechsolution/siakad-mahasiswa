
<div>
    <x-slot name="header">Plotting Dosen Wali (PA)</x-slot>

    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200 sticky top-4 z-50 shadow-lg">
            ✅ {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- FILTER (KIRI) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4">Filter Mahasiswa</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Program Studi</label>
                        <select wire:model.live="filter_prodi" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white">
                            <option value="">-- Pilih Prodi --</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">Angkatan</label>
                        <input wire:model.live="filter_angkatan" type="number" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white" placeholder="2024">
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" wire:model.live="show_has_advisor" id="showHas" class="rounded text-brand-blue">
                        <label for="showHas" class="text-sm text-slate-700 dark:text-slate-300">Tampilkan yg sudah punya PA</label>
                    </div>
                </div>
            </div>

            <!-- PANEL EKSEKUSI (STICKY) -->
            <div class="bg-brand-blue p-6 rounded-xl shadow-lg text-white sticky top-6">
                <h3 class="font-bold text-lg mb-2">Aksi Massal</h3>
                <p class="text-xs text-blue-200 mb-4">Terpilih: <strong>{{ count($selected_students) }}</strong> Mahasiswa</p>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-blue-200 mb-1">Pilih Dosen Wali</label>
                        <select wire:model="selected_lecturer" class="w-full rounded-lg border-0 text-slate-800 text-sm">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($lecturers as $l)
                                <option value="{{ $l->id }}">{{ $l->user->name }}</option>
                            @endforeach
                        </select>
                        @error('selected_lecturer') <span class="text-xs text-orange-300">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="save" wire:loading.attr="disabled" class="w-full py-2 bg-brand-gold text-brand-blue font-bold rounded-lg hover:bg-yellow-400 transition shadow-md">
                        SIMPAN PLOTTING
                    </button>

                    @if($show_has_advisor)
                    <button wire:click="detach" wire:confirm="Yakin ingin melepas Dosen Wali dari mahasiswa terpilih?" class="w-full py-2 bg-red-500/20 border border-red-400 text-red-100 font-bold rounded-lg hover:bg-red-500 hover:text-white transition text-xs">
                        Lepas Dosen Wali
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- TABEL DATA (KANAN) -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-4 py-3 w-10 text-center">
                                <input type="checkbox" wire:model.live="select_all" class="rounded text-brand-blue focus:ring-brand-blue">
                            </th>
                            <th class="px-6 py-3">NIM</th>
                            <th class="px-6 py-3">Nama Mahasiswa</th>
                            <th class="px-6 py-3">Dosen Wali Saat Ini</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($students as $s)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 {{ in_array($s->id, $selected_students) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox" wire:model.live="selected_students" value="{{ $s->id }}" class="rounded text-brand-blue focus:ring-brand-blue">
                            </td>
                            <td class="px-6 py-3 font-mono font-bold text-brand-blue dark:text-brand-gold">{{ $s->nim }}</td>
                            <td class="px-6 py-3 text-slate-800 dark:text-white">{{ $s->user->name }}</td>
                            <td class="px-6 py-3">
                                @if($s->academic_advisor)
                                    <span class="text-slate-600 dark:text-slate-300 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        {{ $s->academic_advisor->user->name }}
                                    </span>
                                @else
                                    <span class="text-red-400 text-xs italic">Belum ada</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                Tidak ada data mahasiswa yang sesuai filter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="p-4">
                    {{ $students->links() }}
                </div>
            </div>
        </div>

    </div>
</div>

