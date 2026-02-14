<div class="space-y-6 font-sans">
    <x-slot name="header">Curriculum Courses</x-slot>

    <!-- Alert Notifikasi -->
    @if (session()->has('message'))
    <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-2 shadow-sm animate-fade-in-down">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('message') }}
    </div>
    @endif

    <!-- Toolbar: Search & Filter -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto flex-1">
            <!-- Search -->
            <div class="relative flex-1 w-full">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Kode / Nama Matkul..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all shadow-sm">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Filter Prodi -->
            <select wire:model.live="filter_prodi" class="w-full sm:w-auto rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue shadow-sm cursor-pointer">
                <option value="">Semua Program Studi</option>
                @foreach($prodis as $p)
                <option value="{{ $p->id }}" wire:key="filter-prodi-{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>

            <!-- Filter Tahun Kurikulum -->
            <select wire:model.live="filter_year" class="w-full sm:w-auto rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue shadow-sm cursor-pointer">
                <option value="">Semua Tahun Kurikulum</option>
                @foreach($curriculum_years as $year)
                <option value="{{ $year }}" wire:key="filter-year-{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <button wire:click="create" class="w-full sm:w-auto px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2 transform active:scale-95">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Course
        </button>
    </div>

    <!-- Table Curriculum Courses -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Mata Kuliah</th>
                        <th class="px-6 py-4 text-center">SKS</th>
                        <th class="px-6 py-4 text-center">Semester</th>
                        <th class="px-6 py-4">Program Studi</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($curriculum_courses as $cc)
                    <tr wire:key="row-{{ $cc->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-700 flex flex-col items-center justify-center text-[10px] font-bold text-slate-500 border border-slate-200 dark:border-slate-600">
                                    <span class="text-brand-blue">{{ $cc->course->code ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $cc->course->name ?? 'Deleted Course' }}</div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded border {{ $cc->is_mandatory ? 'bg-red-50 border-red-100 text-red-600' : 'bg-blue-50 border-blue-100 text-blue-600' }}">
                                            {{ $cc->is_mandatory ? 'Wajib' : 'Pilihan' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center font-black text-lg">{{ $cc->credit_total }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2.5 py-1 rounded-full text-xs font-bold">{{ $cc->semester }}</span>
                        </td>
                        <td class="px-6 py-4 text-xs text-slate-600 dark:text-slate-400">
                            {{ $cc->curriculum->studyProgram->name ?? '-' }} 
                            <span class="text-slate-400">({{ $cc->curriculum->year ?? '-' }})</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 {{ $cc->is_mandatory ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }} border rounded-full text-[10px] font-bold">
                                {{ $cc->is_mandatory ? 'Wajib' : 'Pilihan' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-1">
                                <!-- Tombol Edit -->
                                <button wire:click="edit('{{ $cc->id }}')" class="p-2 text-slate-500 hover:text-brand-blue rounded-lg" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>

                                <!-- Tombol Prasyarat -->
                                <a href="{{ route('admin.academic.curriculum-courses.prerequisites', $cc->id) }}"
                                    class="p-2 text-slate-500 hover:text-green-600 rounded-lg"
                                    title="Kelola Prasyarat">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </a>

                                <!-- Tombol Hapus -->
                                <button wire:click="delete('{{ $cc->id }}')" 
                                        wire:confirm="Yakin ingin menghapus matakuliah ini dari kurikulum? Data nilai mahasiswa terkait mungkin akan terpengaruh."
                                        class="p-2 text-slate-500 hover:text-red-600 rounded-lg" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">Belum ada course ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700">
            {{ $curriculum_courses->links() }}
        </div>
    </div>

    <!-- Modal CRUD -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl w-full max-w-3xl mx-auto transform transition-all scale-100 overflow-hidden flex flex-col max-h-[95vh] sm:max-h-[90vh]">

            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/50 flex-shrink-0 flex justify-between items-center">
                <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">{{ $isEditMode ? 'Edit Course' : 'Tambah Course' }}</h3>
                <button wire:click="closeModal" class="p-2 hover:bg-slate-100 rounded-full transition-colors text-slate-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="overflow-y-auto p-6 sm:p-8 space-y-6">
                <form wire:submit.prevent="save" id="courseForm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Pilih Kurikulum -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Curriculum <span class="text-red-500">*</span></label>
                            <select wire:model="curriculum_id" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                                <option value="">-- Pilih Curriculum --</option>
                                @foreach($curriculums as $c)
                                <option value="{{ $c->id }}" wire:key="opt-curr-{{ $c->id }}">
                                    {{ $c->studyProgram->name ?? 'Unknown' }} - {{ $c->year }}
                                </option>
                                @endforeach
                            </select>
                            @error('curriculum_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Pilih Matakuliah -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Kuliah <span class="text-red-500">*</span></label>
                            <select wire:model="course_id" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                                <option value="">-- Pilih Course --</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" wire:key="opt-course-{{ $course->id }}">
                                    {{ $course->code }} - {{ $course->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('course_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Semester -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Semester <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="semester" min="1" max="8" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue" placeholder="Contoh: 1">
                            @error('semester') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Kelompok Matkul (MKK/MKB) -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Grup <span class="text-red-500">*</span></label>
                            <select wire:model="course_group_id" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue">
                                <option value="">-- Pilih Kelompok --</option>
                                @foreach($courseGroups as $group)
                                <option value="{{ $group->id }}" wire:key="opt-group-{{ $group->id }}">
                                    {{ $group->code }} - {{ $group->name }}
                                </option>
                                @endforeach
                            </select>
                            @error('course_group_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Checkbox Wajib -->
                        <div class="col-span-2">
                            <label class="inline-flex items-center cursor-pointer select-none mt-2 p-3 border rounded-xl w-full hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <input type="checkbox" wire:model="is_mandatory" class="rounded text-brand-blue focus:ring-brand-blue h-5 w-5 border-slate-300">
                                <span class="ml-3 block text-sm font-bold text-slate-700 dark:text-slate-300">
                                    Ini adalah Mata Kuliah WAJIB
                                    <span class="block text-xs text-slate-400 font-normal">Jika dicentang, mahasiswa harus mengambil matkul ini untuk lulus.</span>
                                </span>
                            </label>
                        </div>

                        <!-- SKS Config -->
                        <div class="col-span-2 grid grid-cols-3 gap-4 bg-slate-50 dark:bg-slate-700/30 p-4 rounded-xl border border-slate-100 dark:border-slate-700">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Total SKS <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="credit_total" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-center font-black text-lg focus:ring-brand-blue">
                                @error('credit_total') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Teori</label>
                                <input type="number" wire:model="credit_theory" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-center font-bold focus:ring-brand-blue">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Praktik</label>
                                <input type="number" wire:model="credit_practice" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-center font-bold focus:ring-brand-blue">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-6 sm:px-8 py-5 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex justify-end gap-3 flex-shrink-0">
                <button type="button" wire:click="closeModal" class="px-6 py-2.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition text-sm">Batal</button>
                <button type="submit" form="courseForm" 
                        wire:loading.attr="disabled" 
                        wire:target="save" 
                        class="px-8 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 shadow-lg transition flex items-center gap-2 disabled:opacity-50 text-sm">
                    <span wire:loading.remove wire:target="save">Simpan</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>