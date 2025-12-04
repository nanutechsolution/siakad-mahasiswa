<div class="mx-auto max-w-7xl space-y-8">
    <x-slot name="header">KRS Online</x-slot>

    <!-- 1. BLOCKING SCREEN: JIKA BELUM REGISTRASI -->
    @if ($is_locked)
        <div
            class="bg-white dark:bg-slate-800 rounded-[2rem] p-8 text-center shadow-xl border-2 border-red-100 dark:border-red-900/30 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-red-500"></div>

            <div
                class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-red-50 text-red-500 mb-6 dark:bg-red-900/20 dark:text-red-400">
                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>

            <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-2">Akses KRS Terkunci</h2>
            <p class="text-slate-500 dark:text-slate-400 max-w-xl mx-auto text-lg">{{ $lock_message }}</p>

            <div class="mt-8">
                @if (str_contains($lock_message, 'SPP'))
                    <a href="{{ route('student.bills') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-brand-blue text-white font-bold rounded-xl hover:bg-blue-800 transition-all shadow-lg shadow-blue-900/20">
                        <span>Ke Menu Pembayaran</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                @else
                    <a href="#"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 text-slate-600 font-bold rounded-xl hover:bg-slate-200 transition-all dark:bg-slate-700 dark:text-slate-300">
                        Hubungi BAAK / Prodi
                    </a>
                @endif
            </div>
        </div>
    @else
        <!-- 2. TAMPILAN NORMAL (JIKA TIDAK TERKUNCI) -->

        @if (session()->has('error'))
            <div
                class="mb-4 p-4 rounded-lg bg-red-100 text-red-700 font-bold border border-red-200 flex items-start gap-3 animate-pulse">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if (session()->has('success'))
            <div
                class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session()->has('warning'))
            <div
                class="mb-4 p-4 rounded-lg bg-yellow-100 text-yellow-800 font-bold border border-yellow-200 flex items-start gap-3">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @if (!$active_period || !$active_period->allow_krs)
            <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-xl shadow">
                <h3 class="text-xl font-bold text-slate-800 dark:text-white">Masa Pengisian KRS Ditutup</h3>
                <p class="text-slate-500">Silakan hubungi BAAK jika Anda terlambat mengisi KRS.</p>
            </div>
        @else
            {{-- INFO PAKET SEMESTER --}}
            @if ($semester_mhs <= 2)
                <div
                    class="flex items-start gap-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl">
                    <div class="p-2 bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-200 rounded-lg shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-900 dark:text-blue-100">Anda Berada di Semester
                            {{ $semester_mhs }} (Sistem Paket)</h4>
                        <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
                            Sistem hanya menampilkan <strong>mata kuliah wajib</strong> untuk Semester
                            {{ $semester_mhs }}. Silakan ambil semua mata kuliah yang tersedia di bawah ini.
                        </p>
                    </div>
                </div>
            @endif

            <div x-data="{ mobileTab: 'available' }" class="space-y-6 relative">

                <!-- MOBILE TAB SWITCHER -->
                <div
                    class="lg:hidden flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl mx-auto max-w-md shadow-inner sticky top-0 z-20">
                    <button @click="mobileTab = 'available'"
                        :class="mobileTab === 'available' ?
                            'bg-white dark:bg-slate-700 text-brand-blue dark:text-white shadow-sm ring-1 ring-black/5' :
                            'text-slate-500 hover:text-slate-700 dark:text-slate-400'"
                        class="flex-1 py-2 text-sm font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                        <span>Pilih Kelas</span>
                    </button>
                    <button @click="mobileTab = 'selected'"
                        :class="mobileTab === 'selected' ?
                            'bg-white dark:bg-slate-700 text-brand-blue dark:text-white shadow-sm ring-1 ring-black/5' :
                            'text-slate-500 hover:text-slate-700 dark:text-slate-400'"
                        class="flex-1 py-2 text-sm font-bold rounded-lg transition-all flex items-center justify-center gap-2">
                        <span>KRS Saya</span>
                        @if ($selected_classes->count() > 0)
                            <span
                                class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $selected_classes->count() }}</span>
                        @endif
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-20 lg:pb-0">

                    <!-- KOLOM KIRI: DAFTAR KELAS -->
                    <div class="lg:col-span-2 space-y-4"
                        :class="mobileTab === 'available' ? 'block' : 'hidden lg:block'">

                        <!-- HEADER DAFTAR KELAS + SEARCH + TOMBOL AMBIL SEMUA -->
                        <div class="flex flex-col gap-4 pb-2 border-b border-slate-200 dark:border-slate-700 mb-2">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                                    <span class="bg-brand-blue text-white text-xs px-2 py-1 rounded">Semester
                                        {{ $active_period->name }}</span>
                                    Kelas Ditawarkan
                                </h3>

                                @if (count($available_classes) > 0)
                                    <button wire:click="takeAll"
                                        wire:confirm="Apakah Anda yakin ingin mengambil SEMUA mata kuliah yang tersedia dalam daftar ini?"
                                        wire:loading.attr="disabled"
                                        class="flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-md transition-all transform active:scale-95 w-full sm:w-auto">
                                        <span wire:loading.remove wire:target="takeAll">Ambil Semua (Paket)</span>
                                        <span wire:loading wire:target="takeAll">Memproses...</span>
                                        <svg wire:loading.remove wire:target="takeAll" class="w-4 h-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </button>
                                @endif
                            </div>

                            <!-- SEARCH BAR -->
                            <div class="relative">
                                <input wire:model.live.debounce.300ms="search" type="text"
                                    placeholder="Cari Mata Kuliah..."
                                    class="w-full rounded-xl border-slate-200 bg-slate-50 px-4 py-2.5 text-sm dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue focus:border-brand-blue pl-10">
                                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        @forelse($available_classes as $class)
                            <!-- CHECK QUOTA -->
                            @php
                                $isFull = $class->enrolled >= $class->quota;
                            @endphp

                            <!-- Card Kelas -->
                            <div
                                class="bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all hover:border-brand-blue dark:hover:border-brand-blue group relative {{ $isFull ? 'opacity-75 grayscale-[0.5]' : '' }}">

                                <div class="flex-1">
                                    <div class="flex gap-2 mb-2">
                                        <span
                                            class="inline-flex items-center rounded-md px-2 py-1 text-[10px] font-bold uppercase tracking-wider ring-1 ring-inset {{ $class->course->is_mandatory ? 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/30' : 'bg-teal-50 text-teal-700 ring-teal-600/20 dark:bg-teal-400/10 dark:text-teal-400 dark:ring-teal-400/30' }}">
                                            {{ $class->course->is_mandatory ? 'Wajib' : 'Pilihan' }}
                                        </span>
                                        @if ($semester_mhs <= 2 && $class->course->semester_default == $semester_mhs)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-indigo-700 ring-1 ring-inset ring-indigo-600/20 dark:bg-indigo-400/10 dark:text-indigo-400 dark:ring-indigo-400/30">
                                                Paket Smt {{ $class->course->semester_default }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-600 ring-1 ring-inset ring-slate-500/20 dark:bg-slate-400/10 dark:text-slate-400 dark:ring-slate-400/30">
                                                Smt {{ $class->course->semester_default }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <h4
                                            class="font-bold text-slate-800 dark:text-white text-lg leading-tight group-hover:text-brand-blue transition-colors">
                                            {{ $class->course->name }}</h4>
                                        <span
                                            class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded text-slate-600 dark:text-slate-300 font-mono">{{ $class->course->code }}</span>
                                    </div>
                                    <div class="text-sm text-slate-500 mt-1 flex flex-wrap gap-x-3">
                                        <span
                                            class="font-semibold text-slate-700 dark:text-slate-300">{{ $class->course->credit_total }}
                                            SKS</span>
                                        <span>•</span>
                                        <span>Kelas <strong
                                                class="text-brand-blue dark:text-brand-gold">{{ $class->name }}</strong></span>
                                        <span>•</span>
                                        <span
                                            class="{{ $isFull ? 'text-red-600 font-black bg-red-50 px-2 rounded' : 'text-slate-500' }}">
                                            Kuota: {{ $class->enrolled }}/{{ $class->quota }}
                                            {{ $isFull ? '(PENUH)' : '' }}
                                        </span>
                                    </div>
                                    <div class="mt-3 space-y-1">
                                        @foreach ($class->schedules as $s)
                                            <div
                                                class="text-xs text-slate-600 dark:text-slate-400 font-medium flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-brand-gold" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $s->day }},
                                                {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($s->end_time)->format('H:i') }}
                                                <span
                                                    class="bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-[10px] ml-1">{{ $s->room_name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between md:justify-end w-full md:w-auto gap-3 mt-2 md:mt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-700 pt-3 md:pt-0">
                                    <span
                                        class="md:hidden text-xs font-bold {{ $isFull ? 'text-red-500' : 'text-brand-blue' }}">
                                        {{ $isFull ? 'Kelas Penuh' : 'Tap untuk ambil →' }}
                                    </span>

                                    <!-- TOMBOL AMBIL -->
                                    <button wire:click="takeClass('{{ $class->id }}')"
                                        wire:loading.attr="disabled" @if ($isFull) disabled @endif
                                        class="shrink-0 px-6 py-2.5 text-sm font-bold rounded-lg transition-all shadow-md w-auto
                                               {{ $isFull
                                                   ? 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none dark:bg-slate-700 dark:text-slate-500'
                                                   : 'bg-brand-blue text-white hover:bg-blue-800 shadow-blue-900/10' }}">
                                        {{ $isFull ? 'Penuh' : 'Ambil' }}
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div
                                class="p-8 text-center text-slate-500 bg-white dark:bg-slate-800 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
                                <p>Tidak ada kelas tersedia untuk paket semester Anda saat ini.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- KOLOM KANAN: KERANJANG SAYA -->
                    <div class="space-y-4" :class="mobileTab === 'selected' ? 'block' : 'hidden lg:block'">
                        <div
                            class="bg-white dark:bg-slate-800 p-6 rounded-xl shadow-lg border border-indigo-100 dark:border-slate-700 sticky top-4">
                            <h3
                                class="font-bold text-lg text-slate-800 dark:text-white mb-4 flex items-center justify-between">
                                <span>KRS Saya</span>
                                @php
                                    $firstItem = $selected_classes->first();
                                    $statusKrs = $firstItem
                                        ? ($firstItem->status instanceof \App\Enums\KrsStatus
                                            ? $firstItem->status->value
                                            : $firstItem->status)
                                        : 'EMPTY';
                                @endphp
                                @if ($statusKrs == 'APPROVED') <span
                                        class="text-xs font-bold px-2 py-1 bg-green-100 text-green-700 rounded">Disetujui</span>
                                @elseif($statusKrs == 'SUBMITTED')
                                    <span
                                        class="text-xs font-bold px-2 py-1 bg-yellow-100 text-yellow-700 rounded">Menunggu</span>
                                @else
                                    <span
                                        class="text-xs font-normal px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-slate-500">Draft</span>
                                @endif
                            </h3>

                            <div
                                class="flex justify-between items-center mb-4 p-4 bg-slate-50 dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800">
                                <span class="text-sm font-medium text-slate-500">Total SKS</span>
                                <div class="text-right">
                                    <span
                                        class="font-black text-2xl text-brand-blue dark:text-brand-gold">{{ $total_sks }}</span>
                                    <span class="text-xs text-slate-400 font-medium">/ {{ $max_sks }}</span>
                                </div>
                            </div>

                            <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar pr-1">
                                @forelse($selected_classes as $plan)
                                    <div
                                        class="p-3 border border-slate-100 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900/30 group hover:border-brand-blue/30 transition-colors">
                                        <div class="flex justify-between items-start gap-2">
                                            <div>
                                                <p
                                                    class="font-bold text-sm text-slate-800 dark:text-white leading-tight">
                                                    {{ $plan->classroom->course->name }}</p>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span
                                                        class="text-[10px] font-bold bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300">{{ $plan->classroom->course->credit_total }}
                                                        SKS</span>
                                                    <span class="text-[10px] text-slate-400">Kelas
                                                        {{ $plan->classroom->name }}</span>
                                                </div>
                                            </div>
                                            @if ($plan->status == App\Enums\KrsStatus::DRAFT)
                                                <button wire:click="dropClass({{ $plan->id }})"
                                                    class="text-slate-300 hover:text-red-500 transition-colors p-1"><svg
                                                        class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg></button>
                                            @else
                                                <span class="text-green-500 p-1"><svg class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg></span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div
                                        class="text-center py-8 px-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                                        <p class="text-sm text-slate-400 italic">Keranjang KRS kosong.</p>
                                    </div>
                                @endforelse
                            </div>

                            <!-- Tombol Ajukan -->
                            @if ($statusKrs == 'DRAFT' || $statusKrs == 'EMPTY')
                                <button wire:click="ajukanKrs" wire:confirm="Yakin ingin mengajukan KRS?"
                                    wire:loading.attr="disabled" @if ($total_sks == 0) disabled @endif
                                    class="w-full mt-6 py-3.5 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg shadow-green-500/30 transition-all flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <span wire:loading.remove wire:target="ajukanKrs">Ajukan ke Dosen Wali</span>
                                    <span wire:loading wire:target="ajukanKrs">Memproses...</span>
                                    <svg wire:loading.remove wire:target="ajukanKrs" class="w-5 h-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            @elseif($statusKrs == 'SUBMITTED')
                                <div
                                    class="mt-6 p-4 bg-yellow-50 text-yellow-800 rounded-xl border border-yellow-200 text-center">
                                    <p class="font-bold">Menunggu Persetujuan</p>
                                </div>
                            @elseif($statusKrs == 'APPROVED')
                                <div
                                    class="mt-6 p-4 bg-green-50 text-green-800 rounded-xl border border-green-200 text-center">
                                    <p class="font-bold">KRS Disetujui</p><a href="{{ route('student.print.krs') }}"
                                        target="_blank"
                                        class="text-xs underline hover:text-green-900 mt-1 block">Cetak Kartu Studi</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- FLOATING MOBILE INFO (SKS MONITOR) -->
                <div class="lg:hidden fixed bottom-4 left-4 right-4 z-30" x-show="mobileTab === 'available'"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-10"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <div
                        class="bg-slate-900/90 backdrop-blur-md text-white p-4 rounded-2xl shadow-2xl border border-white/10 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Total SKS Diambil</p>
                            <p class="text-xl font-black text-brand-gold">{{ $total_sks }} <span
                                    class="text-sm text-slate-400 font-medium">/ {{ $max_sks }}</span></p>
                        </div>
                        <button @click="mobileTab = 'selected'"
                            class="bg-white text-slate-900 px-4 py-2 rounded-lg text-xs font-bold shadow-lg">
                            Lihat Keranjang
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
