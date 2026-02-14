<div class="space-y-6 font-sans pb-20">
    <x-slot name="header">Kartu Rencana Studi (KRS)</x-slot>

    <!-- 1. NOTIFIKASI & STATUS SECTION -->
    <div class="space-y-4">
        {{-- A. Jika Akses Terkunci (Administrasi/Keuangan) --}}
        @if($is_locked)
            <div class="bg-red-50 border-l-4 border-red-500 rounded-2xl p-6 shadow-sm flex items-start gap-5 animate-pulse">
                <div class="flex-shrink-0 p-3 bg-red-100 text-red-600 rounded-2xl">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-black text-red-800 text-xl tracking-tight">Akses KRS Terkunci</h3>
                    <p class="text-red-600 text-sm mt-1 font-medium">{{ $lock_message }}</p>
                </div>
            </div>

        {{-- B. Jika Periode KRS Belum Dibuka --}}
        @elseif(!$active_period || !$active_period->allow_krs)
            <div class="bg-amber-50 border-l-4 border-amber-500 rounded-2xl p-6 shadow-sm flex items-start gap-5">
                <div class="flex-shrink-0 p-3 bg-amber-100 text-amber-600 rounded-2xl">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-black text-amber-800 text-xl tracking-tight">Masa KRS Belum Dibuka</h3>
                    <p class="text-amber-600 text-sm mt-1">Saat ini bukan periode pengisian atau perubahan KRS. Pantau kalender akademik untuk jadwal terbaru.</p>
                </div>
            </div>

        {{-- C. Status Setelah Pengajuan (Submitted, Approved, Rejected) --}}
        @elseif($current_plan && $current_plan->status->value !== 'draft')
            <div class="bg-white dark:bg-slate-800 border-l-4 shadow-sm rounded-2xl p-6 flex items-start gap-5 
                {{ $current_plan->status->value === 'approved' ? 'border-green-500' : 'border-blue-500' }}">
                <div class="flex-shrink-0 p-3 rounded-2xl {{ $current_plan->status->value === 'approved' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                    @if($current_plan->status->value === 'submitted')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @elseif($current_plan->status->value === 'approved')
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    @endif
                </div>
                <div class="flex-1">
                    <h3 class="font-black text-slate-800 dark:text-white text-xl tracking-tight">
                        Status: {{ $current_plan->status->label() }}
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 leading-relaxed font-medium">
                        @if($current_plan->status->value === 'submitted')
                            KRS Anda sedang menunggu validasi Dosen Wali. Perubahan data dikunci sementara.
                        @elseif($current_plan->status->value === 'approved')
                            KRS telah disetujui. Anda dapat mencetak KSS (Kartu Satuan Studi) sekarang.
                        @elseif($current_plan->status->value === 'rejected')
                            <span class="text-red-500 font-bold">Ditolak:</span> {{ $current_plan->notes ?? 'Silakan lakukan revisi pada mata kuliah yang dipilih.' }}
                        @endif
                    </p>
                </div>
            </div>
        @endif
    </div>

    <!-- 2. SUMMARY DASHBOARD -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm flex justify-between items-center">
            <div>
                <p class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Semester Mahasiswa</p>
                <h2 class="text-2xl font-black text-slate-800 dark:text-white">Semester {{ $semester_mhs }}</h2>
                <p class="text-slate-500 text-xs font-bold">{{ $active_period->name ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-8">
                <div class="text-right">
                    <p class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Beban SKS</p>
                    <p class="text-3xl font-black {{ $total_sks > $max_sks ? 'text-red-500' : 'text-brand-blue' }}">
                        {{ $total_sks }}<span class="text-slate-300 text-sm">/{{ $max_sks }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 rounded-3xl p-6 text-white shadow-xl flex items-center justify-between group overflow-hidden relative">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-500/20 rounded-full blur-2xl group-hover:scale-150 transition-transform"></div>
            <div class="relative z-10">
                <p class="text-slate-400 text-[10px] uppercase font-black tracking-widest">Batas Waktu</p>
                <h4 class="text-lg font-bold">{{ $active_period ? \Carbon\Carbon::parse($active_period->end_date)->translatedFormat('d F Y') : '-' }}</h4>
            </div>
            @if($current_plan && $current_plan->status->value === 'draft' && !$is_locked)
                <button wire:click="ajukanKrs" wire:confirm="Ajukan KRS ini ke Dosen Wali?" class="relative z-10 px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold text-sm shadow-lg hover:bg-blue-600 transition active:scale-95">
                    Ajukan KRS
                </button>
            @endif
        </div>
    </div>

    <!-- 3. MAIN INTERFACE -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 {{ $is_locked || ($current_plan && $current_plan->status->value !== 'draft' && $current_plan->status->value !== 'rejected') ? 'opacity-70 grayscale-[0.5]' : '' }}">
        
        <!-- KOLOM KIRI: EXPLORE MATKUL -->
        <div class="xl:col-span-7 space-y-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-slate-100 dark:border-slate-700">
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Kode atau Nama Mata Kuliah..." 
                            class="w-full pl-12 pr-4 py-3 rounded-2xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all">
                        <div class="absolute left-4 top-3.5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                            <tr>
                                <th class="px-6 py-4">Mata Kuliah & Kelas</th>
                                <th class="px-6 py-4 text-center">SKS</th>
                                <th class="px-6 py-4 text-center">Kuota</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                            @forelse($available_classes as $class)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $class->course->name }}</div>
                                    <div class="text-[10px] text-slate-400 uppercase font-black">
                                        {{ $class->course->code }} &bull; Kelas {{ $class->name }}
                                        @foreach($class->classSchedules as $sch)
                                            &bull; <span class="text-brand-blue">{{ $sch->day_name }} {{ substr($sch->start_time, 0, 5) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-700 dark:text-white">{{ $class->course->credit_total }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs {{ $class->enrolled_count >= $class->quota ? 'text-red-500 font-black' : 'text-slate-500' }}">
                                        {{ $class->enrolled_count }}/{{ $class->quota }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button wire:click="takeClass('{{ $class->id }}')" 
                                        class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-white rounded-xl text-xs font-bold hover:bg-brand-blue hover:text-white transition-all disabled:opacity-30"
                                        @if($is_locked || ($current_plan && $current_plan->status->value !== 'draft' && $current_plan->status->value !== 'rejected')) disabled @endif>
                                        Ambil
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada mata kuliah yang tersedia.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: LIST PILIHAN -->
        <div class="xl:col-span-5 space-y-4">
            <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col h-full">
                <div class="p-6 bg-slate-50/50 dark:bg-slate-700/30 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white">Daftar Pilihan Anda</h3>
                    <span class="px-2 py-0.5 bg-brand-blue/10 text-brand-blue rounded text-[10px] font-black uppercase">{{ count($selected_details) }} Item</span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-700 flex-1 overflow-y-auto max-h-[500px]">
                    @forelse($selected_details as $detail)
                    <div class="p-5 flex justify-between items-center group hover:bg-slate-50 dark:hover:bg-slate-700/20 transition">
                        <div class="flex-1 pr-4">
                            <h4 class="font-bold text-slate-800 dark:text-white text-sm truncate">{{ $detail->courseClass->course->name }}</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[9px] bg-slate-100 dark:bg-slate-600 px-1.5 py-0.5 rounded font-black text-slate-500 uppercase">{{ $detail->courseClass->course->code }}</span>
                                <span class="text-[11px] text-slate-400 font-medium">Kelas {{ $detail->courseClass->name }} &bull; {{ $detail->courseClass->course->credit_total }} SKS</span>
                            </div>
                        </div>
                        @if($current_plan && ($current_plan->status->value === 'draft' || $current_plan->status->value === 'rejected') && !$is_locked)
                        <button wire:click="dropClass('{{ $detail->id }}')" class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                        @else
                        <div class="p-2 text-green-500">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="p-12 text-center text-slate-300">
                        <p class="italic text-sm">Belum ada pilihan.</p>
                    </div>
                    @endforelse
                </div>

                <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700 mt-auto">
                    <div class="flex justify-between items-center font-black text-slate-700 dark:text-white uppercase tracking-widest text-xs">
                        <span>Total SKS</span>
                        <span class="text-lg text-brand-blue">{{ $total_sks }} SKS</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>