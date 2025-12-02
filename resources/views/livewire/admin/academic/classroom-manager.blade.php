<div>
    <x-slot name="header">Penjadwalan Kelas (Semester Aktif)</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <div class="w-full max-w-sm">
             <input wire:model.live.debounce.300ms="search" type="text" class="block w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" placeholder="Cari Mata Kuliah...">
        </div>
        <button wire:click="create" class="rounded-lg bg-brand-blue px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800">
            + Buka Kelas Baru
        </button>
    </div>

    @if(!$active_period_id)
        <div class="p-4 bg-red-100 text-red-700 rounded-lg">
            ⚠️ Belum ada Semester Aktif. Silakan atur di menu Pengaturan Sistem dulu.
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($classrooms as $class)
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 relative group hover:border-brand-blue transition-all">
                
                <div class="absolute top-4 right-4">
                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $class->is_open ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $class->is_open ? 'OPEN' : 'CLOSED' }}
                    </span>
                </div>

                <h4 class="font-bold text-slate-800 dark:text-white text-lg">{{ $class->course->name }}</h4>
                <p class="text-xs text-slate-500 mb-3">{{ $class->course->code }} • {{ $class->course->credit_total }} SKS</p>

                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-indigo-50 dark:bg-indigo-900/50 text-brand-blue dark:text-brand-gold px-3 py-1 rounded-md font-bold text-sm">
                        Kelas {{ $class->name }}
                    </div>
                    <div class="text-xs text-slate-500">
                        Kuota: {{ $class->enrolled }}/{{ $class->quota }}
                    </div>
                </div>

                <div class="flex items-center gap-2 mb-4 border-b border-slate-100 dark:border-slate-700 pb-3">
                    <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-xs">👨‍🏫</div>
                    <span class="text-sm text-slate-700 dark:text-slate-300 truncate">
                        {{ $class->lecturer->user->name ?? 'Belum ada Dosen' }}
                    </span>
                </div>

                <div class="space-y-2">
                    @foreach($class->schedules as $sch)
                    <div class="flex items-center text-xs text-slate-600 dark:text-slate-400">
                        <svg class="w-4 h-4 mr-2 text-brand-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <span class="font-semibold w-16">{{ $sch->day }}</span>
                        <span>{{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}</span>
                        <span class="ml-auto bg-slate-100 dark:bg-slate-700 px-1.5 rounded">{{ $sch->room_name }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-2">
                    <button wire:click="edit('{{ $class->id }}')" class="text-xs text-blue-600 hover:underline">Edit Jadwal</button>
                    <button wire:confirm="Hapus kelas ini?" wire:click="delete('{{ $class->id }}')" class="text-xs text-red-600 hover:underline">Hapus</button>
                </div>
            </div>
            @empty
            <div class="col-span-3 text-center py-12 text-slate-500">
                Belum ada kelas dibuka untuk semester ini.
            </div>
            @endforelse
        </div>
        
        {{ $classrooms->links() }}
    @endif

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="w-full max-w-3xl rounded-xl bg-white dark:bg-slate-800 shadow-2xl my-8">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                    {{ $isEditMode ? 'Edit Kelas & Jadwal' : 'Buka Kelas Baru' }}
                </h3>
            </div>
            
            <form wire:submit.prevent="store" class="p-6 space-y-6">
                
                <!-- NOTIFIKASI ERROR BENTROK -->
                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative flex items-start gap-3">
                        <svg class="w-5 h-5 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Mata Kuliah</label>
                        <select wire:model="course_id" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">-- Pilih Matkul --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }} ({{ $c->credit_total }} SKS)</option>
                            @endforeach
                        </select>
                        @error('course_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Kelas</label>
                        <input wire:model="name" type="text" placeholder="Contoh: A" class="w-full rounded-lg border-slate-300 uppercase dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        <p class="text-[10px] text-slate-500">Gunakan satu huruf (A, B, C) atau PAGI/SORE.</p>
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kuota Mahasiswa</label>
                        <input wire:model="quota" type="number" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        @error('quota') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Dosen Pengampu</label>
                        <select wire:model="lecturer_id" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($lecturers as $l)
                                <option value="{{ $l->id }}">{{ $l->user->name ?? 'Dosen' }} ({{ $l->user->username ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-bold text-brand-blue dark:text-brand-gold">Jadwal Pertemuan</label>
                        <button type="button" wire:click="addScheduleRow" class="text-xs bg-slate-100 px-2 py-1 rounded hover:bg-slate-200 dark:bg-slate-700 dark:text-white">
                            + Tambah Hari
                        </button>
                    </div>

                    <div class="space-y-2">
                        @foreach($schedules_input as $index => $schedule)
                        <div class="flex gap-2 items-start">
                            <div class="w-1/4">
                                <select wire:model="schedules_input.{{ $index }}.day" class="w-full text-sm rounded border-slate-300 dark:bg-slate-700 dark:text-white">
                                    <option>Senin</option><option>Selasa</option><option>Rabu</option><option>Kamis</option><option>Jumat</option><option>Sabtu</option>
                                </select>
                            </div>
                            <div class="w-1/5">
                                <input type="time" wire:model="schedules_input.{{ $index }}.start_time" class="w-full text-sm rounded border-slate-300 dark:bg-slate-700 dark:text-white">
                                @error('schedules_input.'.$index.'.start_time') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div class="flex items-center">-</div>
                            <div class="w-1/5">
                                <input type="time" wire:model="schedules_input.{{ $index }}.end_time" class="w-full text-sm rounded border-slate-300 dark:bg-slate-700 dark:text-white">
                            </div>
                            <div class="w-1/4">
                                <input type="text" wire:model="schedules_input.{{ $index }}.room_name" placeholder="Ruang..." class="w-full text-sm rounded border-slate-300 dark:bg-slate-700 dark:text-white">
                                @error('schedules_input.'.$index.'.room_name') <span class="text-[10px] text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <button type="button" wire:click="removeScheduleRow({{ $index }})" class="text-red-500 hover:text-red-700 p-2">
                                🗑️
                            </button>
                        </div>
                        @error('schedules_input.'.$index.'.end_time') <span class="text-xs text-red-500">Jam selesai salah</span> @enderror
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded bg-brand-blue text-white hover:bg-blue-800 shadow-lg shadow-blue-500/30">Simpan Kelas</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>