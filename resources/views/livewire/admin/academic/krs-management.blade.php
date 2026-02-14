<div class="space-y-6 font-sans pb-20">
    <x-slot name="header">Manajemen KRS (Admin Override)</x-slot>

    <!-- Notifikasi -->
    @if (session()->has('message'))
    <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 shadow-sm animate-fade-in-down flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        {{ session('message') }}
    </div>
    @endif

    <!-- 1. Pencarian Mahasiswa -->
    <div class="relative max-w-2xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-4 shadow-lg border border-slate-200 dark:border-slate-700">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search_student" type="text" 
                    placeholder="Cari NIM atau Nama Mahasiswa untuk dikelola..."
                    class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all">
                <div class="absolute left-4 top-3.5 text-slate-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>

            <!-- Dropdown Hasil Pencarian -->
            @if(count($students_result) > 0)
            <div class="absolute left-0 right-0 mt-2 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-100 dark:border-slate-700 z-50 overflow-hidden">
                @foreach($students_result as $res)
                <button wire:click="selectStudent('{{ $res->id }}')" class="w-full p-4 flex items-center gap-4 hover:bg-slate-50 dark:hover:bg-slate-700 transition text-left border-b last:border-0 border-slate-50 dark:border-slate-700">
                    <div class="w-10 h-10 rounded-full bg-brand-blue/10 flex items-center justify-center text-brand-blue font-bold">
                        {{ substr($res->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 dark:text-white leading-tight">{{ $res->user->name }}</p>
                        <p class="text-xs text-slate-400 uppercase tracking-widest font-black mt-1">{{ $res->nim }} &bull; {{ $res->studyProgram->name }}</p>
                    </div>
                </button>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @if($selectedStudent)
    <!-- 2. Dashboard Mahasiswa Terpilih -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-up">
        
        <!-- Info Mahasiswa -->
        <div class="lg:col-span-12 flex justify-between items-center bg-brand-blue p-6 rounded-[2rem] text-white shadow-xl shadow-blue-900/20">
            <div class="flex items-center gap-6">
                <div class="hidden md:block w-16 h-16 bg-white/20 rounded-2xl backdrop-blur-md border border-white/30 flex items-center justify-center text-2xl font-black">
                    {{ substr($selectedStudent->user->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-2xl font-black tracking-tight">{{ $selectedStudent->user->name }}</h2>
                    <p class="text-blue-100 font-medium">{{ $selectedStudent->nim }} &bull; {{ $selectedStudent->studyProgram->name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] uppercase font-black text-blue-200 tracking-widest">Total SKS</p>
                    <p class="text-2xl font-black">{{ $total_sks }} SKS</p>
                </div>
                <button wire:click="resetStudent" class="p-3 bg-white/10 hover:bg-white/20 rounded-xl transition">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        <!-- Kolom Kiri: Matkul Tersedia -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50/50 dark:bg-slate-700/30 border-b border-slate-100 dark:border-slate-700">
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search_class" type="text" placeholder="Cari Kode atau Nama Mata Kuliah..." 
                            class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue">
                        <div class="absolute left-3 top-2.5 text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Mata Kuliah & Kelas</th>
                                <th class="px-6 py-4 text-center">SKS</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                            @forelse($available_classes as $class)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $class->course->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-black uppercase">{{ $class->course->code }} &bull; Kelas {{ $class->name }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-brand-blue">{{ $class->course->credit_total }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="addClass('{{ $class->id }}')" 
                                        class="px-3 py-1.5 bg-brand-blue text-white rounded-lg text-xs font-bold hover:bg-blue-600 transition">
                                        Tambahkan
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-6 py-8 text-center text-slate-400 italic">Mata kuliah tidak ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Matkul Terpilih -->
        <div class="lg:col-span-5 space-y-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col h-full">
                <div class="p-6 bg-slate-50/50 dark:bg-slate-700/30 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800 dark:text-white leading-tight">KRS Terpilih</h3>
                        <span class="inline-block mt-1 px-2 py-0.5 bg-brand-blue/10 text-brand-blue rounded text-[10px] font-black uppercase tracking-tighter">
                            {{ $current_plan->status->label() ?? 'Belum Ada' }}
                        </span>
                    </div>
                    
                    @if($current_plan && $current_plan->status->value === 'approved')
                    <a href="#" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-black transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak KSS
                    </a>
                    @endif
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-700 flex-1">
                    @forelse($selected_details as $detail)
                    <div class="p-5 flex justify-between items-center group hover:bg-slate-50 dark:hover:bg-slate-700/20 transition">
                        <div class="flex-1 pr-4">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm truncate">{{ $detail->courseClass->course->name }}</h4>
                            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest mt-0.5">Kelas {{ $detail->courseClass->name }} &bull; {{ $detail->courseClass->course->credit_total }} SKS</p>
                        </div>
                        <button wire:click="removeClass('{{ $detail->id }}')" 
                            wire:confirm="Hapus mata kuliah ini dari KRS mahasiswa?"
                            class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                    @empty
                    <div class="p-12 text-center text-slate-300 italic text-sm">Belum ada mata kuliah yang diambil.</div>
                    @endforelse
                </div>

                <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700 mt-auto">
                    <div class="flex justify-between items-center font-black text-slate-700 dark:text-white uppercase tracking-widest text-xs">
                        <span>Total SKS</span>
                        <span class="text-lg text-brand-blue">{{ $total_sks }} SKS</span>
                    </div>
                </div>
            </div>

            <!-- Hint Box -->
            <div class="bg-amber-50 dark:bg-amber-900/10 p-6 rounded-3xl border border-amber-100 dark:border-amber-900/30 text-amber-700 dark:text-amber-400">
                <h5 class="font-bold text-sm mb-1 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Mode Override Admin
                </h5>
                <p class="text-xs">Tindakan ini akan langsung merubah status KRS mahasiswa menjadi **APPROVED** dan mengabaikan batas waktu normal.</p>
            </div>
        </div>

    </div>
    @endif
</div>