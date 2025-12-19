<div class="space-y-6 font-sans">
    <x-slot name="header">Penjadwalan Kelas</x-slot>

    <!-- Prepare Data for Searchable Dropdowns (Alpine) -->
    @php
        $courseOptions = $courses->map(fn($c) => [
            'id' => $c->id,
            'label' => $c->name,
            'sub' => $c->code . ' • ' . $c->credit_total . ' SKS • Smt ' . $c->semester_default
        ])->values();

        $lecturerOptions = $lecturers->map(fn($l) => [
            'id' => $l->id,
            'label' => $l->front_title . ' ' . $l->user->name . ' ' . $l->back_title,
            'sub' => $l->nidn ? 'NIDN: '.$l->nidn : ($l->nip_internal ? 'NIP: '.$l->nip_internal : '-')
        ])->values();
    @endphp

    <!-- Alert Notifications -->
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-2 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        
        <div class="w-full sm:w-auto flex-1 max-w-md">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Mata Kuliah / Kode..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all shadow-sm">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
        </div>

        <div class="flex w-full sm:w-auto gap-2">
            <button wire:click="openImportModal" class="flex-1 sm:flex-none px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600 rounded-xl font-bold transition shadow-sm flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                Import
            </button>
            <button wire:click="create" class="flex-1 sm:flex-none px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 flex items-center justify-center gap-2 transform active:scale-95">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Buka Kelas Baru
            </button>
        </div>
    </div>

    <!-- Active Period Warning -->
    @if(!$active_period_id)
        <div class="p-6 bg-orange-50 border border-orange-100 rounded-2xl flex flex-col items-center justify-center text-center text-orange-800">
            <svg class="w-12 h-12 text-orange-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <h3 class="text-lg font-bold">Semester Tidak Aktif</h3>
            <p class="text-sm mt-1">Silakan aktifkan periode akademik di menu Pengaturan Sistem terlebih dahulu.</p>
        </div>
    @else
        <!-- Grid Kelas -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($classrooms as $class)
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 relative group hover:border-brand-blue hover:shadow-md transition-all flex flex-col h-full">
                    
                    <!-- Header Card -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-500 mb-1 border border-slate-200 dark:border-slate-600">
                                {{ $class->course->code }}
                            </span>
                            <h4 class="font-bold text-slate-800 dark:text-white text-lg leading-tight line-clamp-2" title="{{ $class->course->name }}">
                                {{ $class->course->name }}
                            </h4>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase border {{ $class->is_open ? 'bg-green-50 text-green-700 border-green-100' : 'bg-red-50 text-red-700 border-red-100' }}">
                                {{ $class->is_open ? 'OPEN' : 'CLOSED' }}
                            </span>
                        </div>
                    </div>

                    <!-- Info Kelas -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="bg-brand-blue/10 text-brand-blue px-3 py-1.5 rounded-lg font-bold text-sm border border-brand-blue/20">
                            Kelas {{ $class->name }}
                        </div>
                        <div class="text-xs text-slate-500 flex flex-col">
                            <span class="font-bold text-slate-700 dark:text-slate-300">Kuota {{ $class->quota }}</span>
                            <span>{{ $class->course->credit_total }} SKS</span>
                        </div>
                    </div>

                    <!-- Dosen -->
                    <div class="flex items-center gap-2 mb-4 p-2 bg-slate-50 dark:bg-slate-700/30 rounded-lg border border-slate-100 dark:border-slate-700">
                        <div class="w-8 h-8 rounded-full bg-slate-200 flex-shrink-0 flex items-center justify-center text-xs">👨‍🏫</div>
                        <div class="overflow-hidden">
                            <p class="text-xs text-slate-400 font-bold uppercase">Dosen Pengampu</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">
                                {{ $class->lecturer ? $class->lecturer->front_title . ' ' . $class->lecturer->user->name . ' ' . $class->lecturer->back_title : 'Belum Ditentukan' }}
                            </p>
                        </div>
                    </div>

                    <!-- Jadwal List -->
                    <div class="flex-1 space-y-2 mb-4">
                        @foreach($class->schedules as $sch)
                            <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 p-2 rounded-lg">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <span class="font-bold text-slate-800 dark:text-white w-14">{{ $sch->day }}</span>
                                    <span>{{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}</span>
                                </div>
                                <span class="bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded font-mono font-bold text-[10px] text-slate-500">{{ $sch->room_name }}</span>
                            </div>
                        @endforeach
                        @if($class->schedules->isEmpty())
                            <div class="text-center text-xs text-slate-400 italic py-2">Belum ada jadwal</div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-between items-center mt-auto">
                        <span class="text-[10px] text-slate-400">ID: #{{ $class->id }}</span>
                        <div class="flex gap-2">
                            <button wire:click="edit('{{ $class->id }}')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-brand-blue hover:bg-blue-50 dark:hover:bg-slate-700 transition">
                                Edit Jadwal
                            </button>
                            <button wire:confirm="Hapus kelas {{ $class->name }}? Data jadwal juga akan terhapus." wire:click="delete('{{ $class->id }}')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-16">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">Belum ada kelas dibuka</h3>
                    <p class="text-sm text-slate-500 mt-1">Mulai dengan klik tombol "Buka Kelas Baru" di atas.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-6">
            {{ $classrooms->links() }}
        </div>
    @endif

    <!-- MODAL CREATE/EDIT -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl w-full max-w-5xl mx-auto transform transition-all scale-100 overflow-hidden flex flex-col max-h-[95vh] sm:max-h-[90vh]">
            
            <!-- Header -->
            <div class="px-6 sm:px-8 py-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/50 flex-shrink-0 flex justify-between items-center">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">
                        {{ $isEditMode ? 'Edit Kelas & Jadwal' : 'Buka Kelas Baru' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Atur detail kelas, dosen, dan jadwal pertemuan.</p>
                </div>
                <button wire:click="closeModal" class="p-2 hover:bg-slate-200 rounded-full transition-colors text-slate-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto p-6 sm:p-8 space-y-8">
                <form wire:submit.prevent="store" id="classForm">
                    <!-- ... (Isi Form Create/Edit Tetap Sama seperti sebelumnya) ... -->
                    @if (session()->has('error'))
                        <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl flex items-start gap-3 text-sm mb-6">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <div><p class="font-bold">Terjadi Kesalahan</p><p>{{ session('error') }}</p></div>
                        </div>
                    @endif

                    <div class="flex flex-col lg:flex-row gap-8">
                        <!-- LEFT COLUMN: Identitas Kelas -->
                        <div class="lg:w-1/3 space-y-6">
                            <div class="bg-slate-50 dark:bg-slate-700/30 p-5 rounded-2xl border border-slate-100 dark:border-slate-700">
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
                                    <span class="w-4 h-[2px] bg-brand-blue rounded-full"></span> Detail Kelas
                                </h4>
                                <div class="space-y-4">
                                    <!-- Searchable Dropdown Mata Kuliah -->
                                    <div x-data="{
                                        open: false,
                                        search: '',
                                        selectedId: @entangle('course_id'),
                                        options: {{ json_encode($courseOptions) }},
                                        get filtered() {
                                            if (this.search === '') return this.options;
                                            return this.options.filter(i => i.label.toLowerCase().includes(this.search.toLowerCase()) || i.sub.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        get display() {
                                            let item = this.options.find(i => i.id == this.selectedId);
                                            return item ? item.label : '-- Pilih Matkul --';
                                        }
                                    }" class="relative">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mata Kuliah</label>
                                        <button type="button" @click="open = !open; $nextTick(() => $refs.searchInput.focus())" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-left shadow-sm focus:ring-2 focus:ring-brand-blue focus:border-brand-blue flex justify-between items-center transition-all">
                                            <span x-text="display" :class="selectedId ? 'text-slate-900 dark:text-white font-bold text-sm' : 'text-slate-400 text-sm italic'"></span>
                                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" style="display: none;" class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-hidden flex flex-col">
                                            <div class="p-2 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 sticky top-0">
                                                <input x-ref="searchInput" x-model="search" type="text" placeholder="Cari..." class="w-full text-xs rounded-lg border-slate-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white focus:border-brand-blue focus:ring-brand-blue">
                                            </div>
                                            <div class="overflow-y-auto flex-1 p-1 custom-scrollbar">
                                                <template x-for="option in filtered" :key="option.id">
                                                    <button type="button" @click="selectedId = option.id; open = false; search = ''" class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors group">
                                                        <div class="font-bold text-slate-800 dark:text-white text-xs" x-text="option.label"></div>
                                                        <div class="text-[10px] text-slate-500 dark:text-slate-400" x-text="option.sub"></div>
                                                    </button>
                                                </template>
                                                <div x-show="filtered.length === 0" class="p-3 text-center text-xs text-slate-400">Tidak ditemukan</div>
                                            </div>
                                        </div>
                                        @error('course_id') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nama Kelas</label>
                                            <input wire:model="name" type="text" placeholder="A" class="w-full rounded-xl border-slate-300 dark:bg-slate-800 dark:border-slate-600 dark:text-white focus:ring-brand-blue text-center uppercase font-bold text-sm">
                                            @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Kuota</label>
                                            <input wire:model="quota" type="number" class="w-full rounded-xl border-slate-300 dark:bg-slate-800 dark:border-slate-600 dark:text-white focus:ring-brand-blue text-center font-bold text-sm">
                                            @error('quota') <span class="text-red-500 text-[10px] font-bold mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Searchable Dropdown Dosen -->
                                    <div x-data="{
                                        open: false,
                                        search: '',
                                        selectedId: @entangle('lecturer_id'),
                                        options: {{ json_encode($lecturerOptions) }},
                                        get filtered() {
                                            if (this.search === '') return this.options;
                                            return this.options.filter(i => i.label.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        get display() {
                                            let item = this.options.find(i => i.id == this.selectedId);
                                            return item ? item.label : '-- Pilih Dosen --';
                                        }
                                    }" class="relative">
                                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Dosen Pengampu</label>
                                        <button type="button" @click="open = !open; $nextTick(() => $refs.searchInput.focus())" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl py-2.5 px-3 text-left shadow-sm focus:ring-2 focus:ring-brand-blue focus:border-brand-blue flex justify-between items-center transition-all">
                                            <span x-text="display" :class="selectedId ? 'text-slate-900 dark:text-white font-medium text-sm' : 'text-slate-400 text-sm italic'"></span>
                                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                        <div x-show="open" @click.outside="open = false" style="display: none;" class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-hidden flex flex-col">
                                            <div class="p-2 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 sticky top-0">
                                                <input x-ref="searchInput" x-model="search" type="text" placeholder="Cari Nama..." class="w-full text-xs rounded-lg border-slate-200 dark:bg-slate-700 dark:border-slate-600 dark:text-white focus:border-brand-blue focus:ring-brand-blue">
                                            </div>
                                            <div class="overflow-y-auto flex-1 p-1 custom-scrollbar">
                                                <template x-for="option in filtered" :key="option.id">
                                                    <button type="button" @click="selectedId = option.id; open = false; search = ''" class="w-full text-left px-3 py-2 rounded-lg text-sm hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors group">
                                                        <div class="font-bold text-slate-800 dark:text-white text-xs" x-text="option.label"></div>
                                                        <div class="text-[10px] text-slate-500 dark:text-slate-400" x-text="option.sub"></div>
                                                    </button>
                                                </template>
                                                <div x-show="filtered.length === 0" class="p-3 text-center text-xs text-slate-400">Tidak ditemukan</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2 pt-2">
                                        <input type="checkbox" wire:model="is_open" id="isOpen" class="rounded text-brand-blue focus:ring-brand-blue border-slate-300 h-4 w-4">
                                        <label for="isOpen" class="text-xs text-slate-700 dark:text-slate-300 font-bold select-none cursor-pointer">Buka Kelas (Open for KRS)</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT COLUMN: Jadwal Builder -->
                        <div class="lg:w-2/3">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                                    <span class="w-4 h-[2px] bg-orange-400 rounded-full"></span> Jadwal & Ruangan
                                </h4>
                                <button type="button" wire:click="addScheduleRow" class="text-xs bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-white px-3 py-1.5 rounded-lg transition shadow-sm flex items-center gap-1 font-bold">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Tambah Jadwal
                                </button>
                            </div>

                            <div class="space-y-3">
                                <!-- Desktop Header -->
                                <div class="hidden sm:grid grid-cols-12 gap-3 text-[10px] font-bold text-slate-400 uppercase px-3">
                                    <div class="col-span-3">Hari</div>
                                    <div class="col-span-3">Waktu Mulai</div>
                                    <div class="col-span-3">Waktu Selesai</div>
                                    <div class="col-span-2">Ruangan</div>
                                    <div class="col-span-1 text-center">Hapus</div>
                                </div>

                                @foreach($schedules_input as $index => $schedule)
                                    <div class="relative group animate-fade-in">
                                        <!-- Desktop Grid -->
                                        <div class="hidden sm:grid grid-cols-12 gap-3 items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-2 rounded-xl hover:border-brand-blue/50 transition-colors shadow-sm">
                                            <div class="col-span-3">
                                                <select wire:model="schedules_input.{{ $index }}.day" class="w-full text-xs rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white focus:ring-brand-blue font-bold">
                                                    <option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option><option>Sabtu</option>
                                                </select>
                                            </div>
                                            <div class="col-span-3">
                                                <div class="relative">
                                                    <input type="time" wire:model="schedules_input.{{ $index }}.start_time" class="w-full text-xs rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white focus:ring-brand-blue pl-7">
                                                    <svg class="w-3 h-3 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                </div>
                                            </div>
                                            <div class="col-span-3">
                                                <div class="relative">
                                                    <input type="time" wire:model="schedules_input.{{ $index }}.end_time" class="w-full text-xs rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white focus:ring-brand-blue pl-7">
                                                    <svg class="w-3 h-3 text-slate-400 absolute left-2.5 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                </div>
                                            </div>
                                            <div class="col-span-2">
                                                <input type="text" wire:model="schedules_input.{{ $index }}.room_name" placeholder="R.201" class="w-full text-xs rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white focus:ring-brand-blue uppercase font-bold text-center">
                                            </div>
                                            <div class="col-span-1 text-center">
                                                <button type="button" wire:click="removeScheduleRow({{ $index }})" class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Mobile Card -->
                                        <div class="sm:hidden bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-4 rounded-xl shadow-sm space-y-3 relative">
                                            <button type="button" wire:click="removeScheduleRow({{ $index }})" class="absolute top-2 right-2 text-slate-300 hover:text-red-500 p-1">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 mb-1">HARI</label>
                                                <select wire:model="schedules_input.{{ $index }}.day" class="w-full text-sm rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white font-bold">
                                                    <option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option><option>Sabtu</option>
                                                </select>
                                            </div>
                                            <div class="flex gap-2">
                                                <div class="flex-1">
                                                    <label class="block text-[10px] font-bold text-slate-400 mb-1">MULAI</label>
                                                    <input type="time" wire:model="schedules_input.{{ $index }}.start_time" class="w-full text-sm rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white">
                                                </div>
                                                <div class="flex-1">
                                                    <label class="block text-[10px] font-bold text-slate-400 mb-1">SELESAI</label>
                                                    <input type="time" wire:model="schedules_input.{{ $index }}.end_time" class="w-full text-sm rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-400 mb-1">RUANGAN</label>
                                                <input type="text" wire:model="schedules_input.{{ $index }}.room_name" placeholder="Contoh: R.201" class="w-full text-sm rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white uppercase font-bold">
                                            </div>
                                        </div>

                                        <!-- Errors -->
                                        @error('schedules_input.'.$index.'.start_time')
                                            <div class="text-[10px] text-red-500 font-bold mt-1 px-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        @error('schedules_input.'.$index.'.room_name')
                                            <div class="text-[10px] text-red-500 font-bold mt-1 px-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                @endforeach

                                @if(empty($schedules_input))
                                    <div class="text-center py-8 bg-slate-50 border border-dashed border-slate-200 rounded-xl flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-8 h-8 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span class="text-xs italic">Belum ada jadwal diatur.</span>
                                        <button type="button" wire:click="addScheduleRow" class="mt-2 text-xs text-brand-blue font-bold hover:underline">Tambah Jadwal Sekarang</button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer Action -->
            <div class="px-6 sm:px-8 py-5 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex justify-end gap-3 flex-shrink-0">
                <button type="button" wire:click="closeModal" class="px-6 py-2.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition text-sm">Batal</button>
                <button type="submit" form="classForm" wire:loading.attr="disabled" wire:target="store" 
                    class="px-8 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 shadow-lg transition flex items-center gap-2 disabled:opacity-50 text-sm">
                    <span wire:loading.remove wire:target="store">Simpan Kelas</span>
                    <span wire:loading wire:target="store">Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- MODAL IMPORT (New) -->
    @if($isImportModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 animate-fade-in">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                    Import Jadwal Massal
                </h3>
                <p class="text-xs text-slate-500 mt-1">Upload CSV untuk membuat banyak kelas sekaligus.</p>
            </div>
            
            <form wire:submit.prevent="import" class="p-6 space-y-4">
                
                @if (session()->has('error'))
                    <div class="bg-red-50 text-red-600 px-3 py-2 rounded-lg text-xs flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-800 leading-relaxed">
                    <div class="flex justify-between items-start">
                        <div>
                            <strong>Format CSV:</strong> <br>
                            <code>Kode Matkul, Nama Kelas, Email/NIDN Dosen, Kuota, Hari, Jam Mulai, Jam Selesai, Ruangan</code>
                        </div>
                        <button type="button" wire:click="downloadTemplate" class="text-blue-600 hover:text-blue-800 underline text-[10px] font-bold">
                            Download Template
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Pilih File CSV</label>
                    <input type="file" wire:model="file_import" accept=".csv, .txt" 
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-brand-blue file:text-white hover:file:bg-blue-700 transition cursor-pointer">
                    @error('file_import') <span class="text-red-500 text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" wire:click="closeImportModal" class="px-4 py-2 text-xs font-bold text-slate-500 hover:bg-slate-100 rounded-lg transition">Batal</button>
                    <button type="submit" wire:loading.attr="disabled" wire:targer="import" class="px-6 py-2 bg-green-600 text-white rounded-lg text-xs font-bold hover:bg-green-700 shadow-lg shadow-green-600/20 transition flex items-center gap-2">
                        <span wire:loading.remove  wire:target="import">Upload & Import</span>
                        <span wire:loading wire:target="import">Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>