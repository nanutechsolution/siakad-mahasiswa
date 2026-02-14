<div class="space-y-6 font-sans pb-10">
    <x-slot name="header">Manajemen Kelas & Jadwal Kuliah</x-slot>

    <!-- 1. NOTIFIKASI -->
    @if (session()->has('message'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-3 shadow-sm animate-fade-in-down">
        <div class="h-10 w-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-200">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        </div>
        <div>
            <p class="font-bold text-emerald-900 text-sm">Berhasil!</p>
            <p class="text-emerald-600 text-xs">{{ session('message') }}</p>
        </div>
    </div>
    @endif

    <!-- 2. TOOLBAR & STATS -->
    <div class="flex flex-col lg:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="relative w-full lg:w-96">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Kode atau Nama Mata Kuliah..."
                class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all shadow-sm">
            <div class="absolute left-4 top-3.5 text-slate-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto">
            <!-- Stats Ringkas -->
            <div class="hidden sm:flex items-center gap-4 px-6 border-r border-slate-100 dark:border-slate-700 mr-2">
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Kelas</p>
                    <p class="text-lg font-black text-slate-800 dark:text-white leading-none">{{ $classrooms->total() }}</p>
                </div>
            </div>
            
            <button wire:click="create" class="flex-1 lg:flex-none px-6 py-3 bg-brand-blue text-white rounded-2xl font-bold hover:bg-blue-700 transition shadow-xl shadow-blue-900/20 flex items-center justify-center gap-2 transform active:scale-95">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Kelas Baru
            </button>
        </div>
    </div>

    <!-- 3. MAIN DATA TABLE -->
    <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-8 py-5">Informasi Mata Kuliah & Dosen</th>
                        <th class="px-6 py-5 text-center">Kelas</th>
                        <th class="px-6 py-5 text-center">Kapasitas</th>
                        <th class="px-6 py-5">Jadwal & Lokasi</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-8 py-5 text-right">Kelola</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                    @forelse($classrooms as $class)
                    <tr wire:key="row-{{ $class->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-start gap-4">
                                <div class="h-12 w-12 rounded-2xl bg-blue-50 dark:bg-slate-700 text-brand-blue flex items-center justify-center flex-shrink-0 font-black border border-blue-100 dark:border-slate-600">
                                    {{ substr($class->course->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-black text-slate-800 dark:text-white text-base leading-tight">{{ $class->course->name }}</div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-black bg-slate-100 dark:bg-slate-600 px-1.5 py-0.5 rounded text-slate-500 dark:text-slate-300 uppercase tracking-tighter">{{ $class->course->code }}</span>
                                        <span class="text-xs text-slate-400">&bull;</span>
                                        <span class="text-xs text-slate-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            {{ $class->lecturers->where('pivot.is_primary', true)->first()->user->name ?? 'Belum ada dosen' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-lg font-black text-slate-700 dark:text-slate-200">{{ $class->name }}</span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="text-sm font-black text-slate-800 dark:text-white">{{ $class->enrolled_count }} / {{ $class->quota }}</span>
                                <div class="w-16 bg-slate-100 dark:bg-slate-700 h-1 rounded-full mt-1.5 overflow-hidden">
                                    <div class="bg-brand-blue h-full rounded-full" style="width: {{ min(($class->enrolled_count / max($class->quota, 1)) * 100, 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="space-y-1.5">
                                @foreach($class->classSchedules as $sch)
                                <div class="flex items-center gap-2 text-[11px]">
                                    <span class="w-12 text-center py-0.5 bg-slate-100 dark:bg-slate-700 rounded-md font-bold text-slate-600 dark:text-slate-300 uppercase">{{ $sch->day_name }}</span>
                                    <span class="text-slate-500 font-medium">{{ substr($sch->start_time, 0, 5) }}-{{ substr($sch->end_time, 0, 5) }}</span>
                                    <span class="text-brand-blue font-bold px-1.5 py-0.5 bg-blue-50 dark:bg-blue-900/30 rounded border border-blue-100 dark:border-blue-800">{{ $sch->room_name }}</span>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <button wire:click="toggleStatus('{{ $class->id }}')" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter transition-all {{ $class->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200' }}">
                                {{ $class->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </button>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-1.5">
                                <button wire:click="edit('{{ $class->id }}')" class="p-2.5 text-slate-400 hover:text-brand-blue hover:bg-blue-50 dark:hover:bg-slate-700 transition-all rounded-xl shadow-sm border border-transparent hover:border-blue-100" title="Edit Data">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                </button>
                                <button wire:click="delete('{{ $class->id }}')" wire:confirm="PERINGATAN! Menghapus kelas akan menghapus seluruh jadwal dan data pengambilan mahasiswa. Lanjutkan?" class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-700 transition-all rounded-xl shadow-sm border border-transparent hover:border-rose-100" title="Hapus Kelas">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="h-20 w-20 rounded-full bg-slate-50 dark:bg-slate-700 flex items-center justify-center text-slate-300 mb-4">
                                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                </div>
                                <h3 class="text-slate-400 font-bold italic">Data kelas belum tersedia atau tidak ditemukan.</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-8 bg-slate-50/50 dark:bg-slate-900/50 border-t border-slate-100 dark:border-slate-700">
            {{ $classrooms->links() }}
        </div>
    </div>

    <!-- 4. MODAL CRUD (PREMIUM FLOW) -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" wire:click="closeModal"></div>
        
        <div class="relative bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] animate-scale-up">
            <!-- Modal Header -->
            <div class="px-10 py-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">{{ $isEditMode ? 'Edit Kelas Perkuliahan' : 'Tambah Kelas Kuliah Baru' }}</h3>
                    <p class="text-xs text-slate-500 font-medium uppercase tracking-widest mt-0.5">Kelola mata kuliah, dosen, dan plotting jadwal</p>
                </div>
                <button wire:click="closeModal" class="h-10 w-10 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-600 rounded-full transition text-slate-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-10 space-y-10">
                <form wire:submit.prevent="store" id="classForm" class="space-y-10">
                    <!-- SECTION 1: MASTER DATA -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-8 rounded-lg bg-brand-blue/10 text-brand-blue flex items-center justify-center font-bold text-sm">1</span>
                            <h4 class="font-black text-slate-700 dark:text-slate-300 uppercase text-xs tracking-widest">Informasi Utama Kelas</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Mata Kuliah <span class="text-rose-500">*</span></label>
                                <select wire:model="course_id" class="w-full rounded-2xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 focus:ring-brand-blue shadow-sm">
                                    <option value="">-- Pilih Mata Kuliah --</option>
                                    @foreach($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                                    @endforeach
                                </select>
                                @error('course_id') <span class="text-rose-500 text-[10px] font-black uppercase mt-1 block tracking-tight">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Dosen Pengampu Utama <span class="text-rose-500">*</span></label>
                                <select wire:model="lecturer_id" class="w-full rounded-2xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 focus:ring-brand-blue shadow-sm">
                                    <option value="">-- Pilih Dosen Pengampu --</option>
                                    @foreach($lecturers as $l)
                                    <option value="{{ $l->id }}">{{ $l->user->name }}</option>
                                    @endforeach
                                </select>
                                @error('lecturer_id') <span class="text-rose-500 text-[10px] font-black uppercase mt-1 block tracking-tight">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Nama Kelas <span class="text-rose-500">*</span></label>
                                <input type="text" wire:model="name" placeholder="Misal: A, B, PAGI" class="w-full rounded-2xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 focus:ring-brand-blue shadow-sm uppercase font-bold">
                                @error('name') <span class="text-rose-500 text-[10px] font-black uppercase mt-1 block tracking-tight">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Kuota Mahasiswa <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <input type="number" wire:model="quota" class="w-full rounded-2xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 focus:ring-brand-blue shadow-sm pl-4 pr-12">
                                    <span class="absolute right-4 top-2.5 text-slate-400 text-sm font-bold uppercase tracking-widest">Mhs</span>
                                </div>
                                @error('quota') <span class="text-rose-500 text-[10px] font-black uppercase mt-1 block tracking-tight">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: JADWAL KULIAH -->
                    <div class="space-y-6">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <span class="h-8 w-8 rounded-lg bg-brand-blue/10 text-brand-blue flex items-center justify-center font-bold text-sm">2</span>
                                <h4 class="font-black text-slate-700 dark:text-slate-300 uppercase text-xs tracking-widest">Plotting Jadwal Mingguan</h4>
                            </div>
                            <button type="button" wire:click="addScheduleRow" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:text-white rounded-xl text-xs font-black uppercase tracking-widest transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Tambah Jadwal
                            </button>
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            @foreach($schedules_input as $index => $sch)
                            <div wire:key="schedule-{{ $index }}" class="p-6 rounded-[2rem] border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/20 relative group transition-all hover:shadow-md animate-fade-in">
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 tracking-widest">Hari</label>
                                        <select wire:model="schedules_input.{{ $index }}.day_of_week" class="w-full rounded-xl border-slate-200 dark:bg-slate-900 text-sm focus:ring-brand-blue">
                                            <option value="1">Senin</option>
                                            <option value="2">Selasa</option>
                                            <option value="3">Rabu</option>
                                            <option value="4">Kamis</option>
                                            <option value="5">Jumat</option>
                                            <option value="6">Sabtu</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 tracking-widest">Jam Mulai</label>
                                        <input type="time" wire:model="schedules_input.{{ $index }}.start_time" class="w-full rounded-xl border-slate-200 dark:bg-slate-900 text-sm focus:ring-brand-blue">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 tracking-widest">Selesai</label>
                                        <input type="time" wire:model="schedules_input.{{ $index }}.end_time" class="w-full rounded-xl border-slate-200 dark:bg-slate-900 text-sm focus:ring-brand-blue">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-1 tracking-widest">Ruangan / Link</label>
                                        <input type="text" wire:model="schedules_input.{{ $index }}.room_name" placeholder="Gedung A R.101" class="w-full rounded-xl border-slate-200 dark:bg-slate-900 text-sm focus:ring-brand-blue uppercase font-bold">
                                    </div>
                                </div>

                                @if(count($schedules_input) > 1)
                                <button type="button" wire:click="removeScheduleRow({{ $index }})" class="absolute -top-3 -right-3 h-10 w-10 bg-white dark:bg-slate-800 border border-rose-100 text-rose-500 rounded-full shadow-lg flex items-center justify-center hover:bg-rose-50 transition group-hover:scale-110">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                                @endif

                                @error("schedules_input.{$index}.start_time") <p class="text-rose-500 text-[10px] font-black uppercase mt-2 tracking-tighter">{{ $message }}</p> @enderror
                                @error("schedules_input.{$index}.room_name") <p class="text-rose-500 text-[10px] font-black uppercase mt-2 tracking-tighter">{{ $message }}</p> @enderror
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="p-6 bg-blue-50 dark:bg-slate-700/40 rounded-3xl border border-blue-100 dark:border-slate-600 flex items-center gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-white dark:bg-slate-900 flex items-center justify-center shadow-sm text-brand-blue">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-tighter leading-none">Aktifkan Kelas Sekarang?</p>
                            <p class="text-xs text-slate-500 mt-1">Kelas yang aktif akan langsung muncul di halaman KRS Mahasiswa.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="is_active" class="sr-only peer">
                            <div class="w-14 h-8 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-brand-blue shadow-inner"></div>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="px-10 py-8 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex flex-col sm:flex-row justify-end gap-4 shadow-2xl">
                <button type="button" wire:click="closeModal" class="px-8 py-3.5 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-2xl font-black uppercase tracking-widest text-xs transition">Batal</button>
                <button type="submit" form="classForm" wire:loading.attr="disabled" class="px-12 py-3.5 bg-brand-blue text-white rounded-2xl font-black uppercase tracking-widest text-xs shadow-xl shadow-blue-900/20 hover:bg-blue-700 transition flex items-center justify-center gap-3 transform active:scale-95 disabled:opacity-50">
                    <span wire:loading.remove>Simpan Perubahan</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    <style>
        @keyframes scaleUp {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .animate-scale-up {
            animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
    </style>
</div>