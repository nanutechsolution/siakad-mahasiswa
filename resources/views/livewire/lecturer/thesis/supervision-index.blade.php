<div>
    <x-slot name="header">Bimbingan Tugas Akhir</x-slot>

    <!-- Search -->
    <div class="mb-6 flex justify-between items-center">
        <div class="w-full max-w-md">
             <input wire:model.live.debounce.300ms="search" type="text" class="block w-full rounded-lg border border-slate-300 bg-white p-2.5 text-sm dark:bg-slate-800 dark:border-slate-700 dark:text-white" placeholder="Cari Mahasiswa / Judul...">
        </div>
    </div>

    <!-- Grid Card Bimbingan -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($supervisions as $sv)
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:border-brand-blue transition-all">
            
            <!-- Role Badge -->
            <div class="flex justify-between items-start mb-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $sv->role == 1 ? 'bg-indigo-100 text-indigo-700' : 'bg-teal-100 text-teal-700' }}">
                    {{ $sv->role == 1 ? 'Pembimbing Utama' : 'Pembimbing Pendamping' }}
                </span>
                <span class="text-xs text-slate-400 font-mono">
                    {{ $sv->thesis->academic_period->code ?? '-' }}
                </span>
            </div>

            <!-- Info Mahasiswa -->
            <div class="flex items-center gap-4 mb-4">
                <div class="h-12 w-12 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-500 dark:text-slate-300 text-lg">
                    {{ substr($sv->thesis->student->user->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-tight">
                        {{ $sv->thesis->student->user->name }}
                    </h3>
                    <p class="text-sm text-slate-500 font-mono">{{ $sv->thesis->student->nim }}</p>
                </div>
            </div>

            <!-- Judul Skripsi -->
            <div class="bg-slate-50 dark:bg-slate-700/50 p-4 rounded-xl mb-4">
                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Judul Skripsi</p>
                <p class="text-sm font-medium text-slate-800 dark:text-slate-200 line-clamp-2">
                    {{ $sv->thesis->title }}
                </p>
            </div>

            <!-- Progress Bar (Simulasi) -->
            <div class="mb-6">
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-500">Progress Bimbingan</span>
                    <span class="font-bold text-brand-blue">{{ $sv->thesis->logs->where('status', 'APPROVED')->count() }} x Bimbingan</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                    @php
                        $count = $sv->thesis->logs->where('status', 'APPROVED')->count();
                        $percent = min(($count / 8) * 100, 100); // Asumsi min 8x bimbingan
                    @endphp
                    <div class="bg-brand-blue h-2 rounded-full" style="width: {{ $percent }}%"></div>
                </div>
            </div>

            <!-- Action -->
            <a href="{{ route('lecturer.thesis.guidance', $sv->thesis->id) }}" class="flex items-center justify-center w-full py-2.5 bg-slate-900 dark:bg-slate-700 text-white rounded-xl font-bold text-sm hover:bg-brand-blue transition-colors shadow-lg shadow-slate-900/20">
                Buka Kartu Bimbingan
            </a>
        </div>
        @empty
        <div class="col-span-2 text-center py-12">
            <p class="text-slate-500">Belum ada mahasiswa bimbingan yang ditetapkan.</p>
        </div>
        @endforelse
    </div>
    
    <div class="mt-6">{{ $supervisions->links() }}</div>
</div>