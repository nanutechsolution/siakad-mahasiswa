<div class="space-y-6 font-sans">
    <x-slot name="header">Master Mata Kuliah</x-slot>

    <!-- Alert Notifikasi -->
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-2 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Toolbar: Pencarian & Filter (Responsive) -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto flex-1">
            <!-- Search -->
            <div class="relative flex-1 w-full">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Kode / Nama Matkul..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all shadow-sm">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            
            <!-- Filter Prodi -->
            <select wire:model.live="filter_prodi" class="w-full sm:w-auto rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue shadow-sm cursor-pointer">
                <option value="">Semua Program Studi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <button wire:click="create" class="w-full sm:w-auto px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2 transform active:scale-95">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Tambah Matkul
        </button>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Mata Kuliah</th>
                        <th class="px-6 py-4 text-center">SKS</th>
                        <th class="px-6 py-4 text-center">Semester</th>
                        <th class="px-6 py-4">Program Studi</th>
                        <th class="px-6 py-4">Prasyarat</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($courses as $course)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex flex-col items-center justify-center text-[10px] font-bold text-slate-500 border border-slate-200 dark:border-slate-600">
                                    <span class="text-brand-blue">{{ $course->code }}</span>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $course->name }}</div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded border bg-slate-50 border-slate-200 text-slate-500">{{ $course->group_code }}</span>
                                        <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded border {{ $course->is_mandatory ? 'bg-red-50 border-red-100 text-red-600' : 'bg-blue-50 border-blue-100 text-blue-600' }}">
                                            {{ $course->is_mandatory ? 'Wajib' : 'Pilihan' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-black text-lg text-slate-700 dark:text-slate-200">{{ $course->credit_total }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2.5 py-1 rounded-full text-xs font-bold">{{ $course->semester_default }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ $course->study_program->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($course->prerequisites->count() > 0)
                                <div class="flex flex-wrap gap-1 max-w-[200px]">
                                    @foreach($course->prerequisites as $pre)
                                        <span class="bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 text-[10px] font-bold px-1.5 py-0.5 rounded border border-orange-100 dark:border-orange-800 cursor-help" title="{{ $pre->name }} (Min. {{ $pre->pivot->min_grade ?? 'D' }})">
                                            {{ $pre->code }} <span class="text-orange-400">({{ $pre->pivot->min_grade }})</span>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-300 dark:text-slate-600 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($course->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-700 border border-green-200 rounded-full text-[10px] font-bold">AKTIF</span>
                            @else
                                <span class="px-2 py-1 bg-slate-100 text-slate-500 border border-slate-200 rounded-full text-[10px] font-bold">NON-AKTIF</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1">
                                <button wire:click="edit('{{ $course->id }}')" class="p-2 text-slate-500 hover:text-brand-blue hover:bg-blue-50 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <button wire:click="delete('{{ $course->id }}')" wire:confirm="Hapus mata kuliah ini?" class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data mata kuliah ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700">
            {{ $courses->links() }}
        </div>
    </div>

    <!-- Modal Responsif (Bottom Sheet Mobile - Card Desktop) -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl w-full max-w-3xl mx-auto transform transition-all scale-100 overflow-hidden flex flex-col max-h-[95vh] sm:max-h-[90vh]">
            
            <!-- Header Modal -->
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/50 flex-shrink-0 flex justify-between items-center">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">{{ $isEditMode ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}</h3>
                    <p class="text-xs text-slate-500 mt-1">Kelola data kurikulum dan prasyarat.</p>
                </div>
                <button wire:click="closeModal" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto p-6 sm:p-8 space-y-8">
                <form wire:submit.prevent="store" id="courseForm">
                    
                    <!-- 1. Informasi Dasar -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Program Studi</label>
                            <select wire:model.live="study_program_id" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                                <option value="">-- Pilih Program Studi --</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                            @error('study_program_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Matkul</label>
                            <input wire:model="code" type="text" placeholder="Contoh: TI101" class="w-full rounded-xl border-slate-300 uppercase dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                            @error('code') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kelompok</label>
                            <select wire:model="group_code" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                                <option value="MKK">MKK (Keilmuan & Keterampilan)</option>
                                <option value="MKB">MKB (Keahlian Berkarya)</option>
                                <option value="MPK">MPK (Pengembangan Kepribadian)</option>
                                <option value="MPB">MPB (Perilaku Berkarya)</option>
                                <option value="MBB">MBB (Bermasyarakat)</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Mata Kuliah</label>
                            <input wire:model="name" type="text" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                            @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- 2. Detail Kredit -->
                    <div class="bg-slate-50 dark:bg-slate-700/30 p-4 rounded-xl border border-slate-100 dark:border-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Semester</label>
                            <input wire:model="semester_default" type="number" min="1" max="8" class="w-full rounded-xl border-slate-300 text-center font-bold focus:ring-brand-blue">
                            @error('semester_default') <span class="text-red-500 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Total SKS</label>
                            <input wire:model="credit_total" type="number" class="w-full rounded-xl border-slate-300 text-center font-black focus:ring-brand-blue">
                            @error('credit_total') <span class="text-red-500 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-span-2">
                            <div class="flex items-center h-full pt-4">
                                <label class="inline-flex items-center cursor-pointer select-none">
                                    <input type="checkbox" wire:model="is_mandatory" class="rounded text-brand-blue focus:ring-brand-blue h-5 w-5 border-slate-300">
                                    <div class="ml-3">
                                        <span class="block text-sm font-bold text-slate-700 dark:text-slate-300">Mata Kuliah Wajib</span>
                                        <span class="block text-[10px] text-slate-400">Mahasiswa harus lulus matkul ini.</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Prasyarat (Alpine.js Enhanced) -->
                    <div x-data="{
                        searchPrereq: '',
                        allCourses: @js($all_courses),
                        selectedIds: @entangle('prerequisite_ids'),
                        selectedGrades: @entangle('prerequisite_grades'),
                        currentProdiId: @entangle('study_program_id'),
                        get filtered() {
                            if (!this.searchPrereq || !this.currentProdiId) return [];
                            return this.allCourses.filter(c => 
                                String(c.study_program_id) === String(this.currentProdiId) && 
                                (c.name.toLowerCase().includes(this.searchPrereq.toLowerCase()) || 
                                 c.code.toLowerCase().includes(this.searchPrereq.toLowerCase()))
                            ).slice(0, 5);
                        },
                        toggle(id) {
                            id = String(id);
                            if (this.selectedIds.includes(id)) {
                                this.selectedIds = this.selectedIds.filter(i => i !== id);
                            } else {
                                this.selectedIds.push(id);
                                if (!this.selectedGrades[id]) this.selectedGrades[id] = 'C';
                            }
                            this.searchPrereq = '';
                        },
                        getCourseData(id) { return this.allCourses.find(i => i.id == id); }
                    }">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 flex justify-between">
                            <span>Mata Kuliah Prasyarat</span>
                            <span class="text-[10px] text-slate-400 font-normal self-end" x-show="selectedIds.length > 0" x-text="selectedIds.length + ' dipilih'"></span>
                        </label>

                        <!-- Search Box -->
                        <div class="relative mb-3">
                            <input x-model="searchPrereq" type="text" 
                                :placeholder="currentProdiId ? 'Ketik nama/kode matkul prasyarat...' : 'Pilih Program Studi terlebih dahulu'" 
                                :disabled="!currentProdiId"
                                :class="!currentProdiId ? 'bg-slate-100 cursor-not-allowed text-slate-400' : 'bg-white'"
                                class="w-full pl-9 py-2 rounded-xl border-slate-200 text-sm focus:ring-brand-blue shadow-sm">
                            <div class="absolute left-3 top-2.5 text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>

                            <!-- Dropdown Results -->
                            <div x-show="filtered.length > 0" @click.away="searchPrereq = ''" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                <template x-for="c in filtered" :key="c.id">
                                    <div @click="toggle(c.id)" class="px-4 py-2 hover:bg-slate-50 cursor-pointer flex justify-between items-center text-sm border-b border-slate-50 last:border-0">
                                        <div>
                                            <span class="font-bold text-brand-blue mr-2" x-text="c.code"></span>
                                            <span x-text="c.name" class="text-slate-700"></span>
                                        </div>
                                        <span x-show="selectedIds.includes(String(c.id))" class="text-green-500 font-bold text-xs">Termasuk</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Selected List Table -->
                        <div x-show="selectedIds.length > 0" class="border border-slate-200 rounded-xl overflow-hidden">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-50 text-slate-500 font-bold uppercase">
                                    <tr>
                                        <th class="px-4 py-2">Matkul</th>
                                        <th class="px-2 py-2 text-center w-24">Min. Nilai</th>
                                        <th class="px-2 py-2 text-center w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="id in selectedIds" :key="id">
                                        <tr>
                                            <td class="px-4 py-2">
                                                <div class="font-bold text-slate-700" x-text="getCourseData(id)?.name"></div>
                                                <div class="text-[10px] text-slate-400 font-mono" x-text="getCourseData(id)?.code"></div>
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                <select x-model="selectedGrades[id]" class="py-1 pl-2 pr-6 rounded-lg border-slate-200 text-xs font-bold focus:ring-brand-blue bg-slate-50">
                                                    <option value="A">A</option>
                                                    <option value="B">B</option>
                                                    <option value="C">C</option>
                                                    <option value="D">D</option>
                                                </select>
                                            </td>
                                            <td class="px-2 py-2 text-center">
                                                <button type="button" @click="toggle(id)" class="text-slate-400 hover:text-red-500 transition">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <p x-show="selectedIds.length === 0" class="text-xs text-slate-400 italic text-center py-2 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            Belum ada prasyarat dipilih.
                        </p>
                    </div>

                </form>
            </div>

            <!-- Footer Action -->
            <div class="px-6 sm:px-8 py-5 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex justify-end gap-3 flex-shrink-0">
                <button type="button" wire:click="closeModal" class="px-6 py-2.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition text-sm">Batal</button>
                <button type="submit" form="courseForm" wire:loading.attr="disabled" wire:target="store" class="px-8 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 shadow-lg transition flex items-center gap-2 disabled:opacity-50 text-sm">
                    <span wire:loading.remove wire:target="store">Simpan</span>
                    <span wire:loading wire:target="store">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>