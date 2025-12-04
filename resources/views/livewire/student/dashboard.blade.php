<div class="mx-auto max-w-7xl space-y-6 md:space-y-8 font-sans">
    <!-- ONBOARDING MODAL (Hanya Muncul Sekali seumur hidup) -->
    @if ($show_onboarding)
        <div class="fixed inset-0 z-[99] flex items-center justify-center p-4" x-data>
            <!-- Backdrop Blur Gelap -->
            <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-xl"></div>

            <!-- Confetti Canvas -->
            <canvas id="confetti-canvas" class="absolute inset-0 pointer-events-none z-0"></canvas>

            <!-- Card Content -->
            <div
                class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-800 rounded-[2.5rem] shadow-2xl overflow-hidden text-center animate-fade-in-up">

                <!-- Header Image / Decoration -->
                <div
                    class="h-40 bg-gradient-to-br from-brand-blue to-indigo-600 relative flex items-center justify-center overflow-hidden">
                    <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20">
                    </div>
                    <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-brand-gold/30 rounded-full blur-3xl"></div>

                    <div
                        class="relative z-10 bg-white/10 backdrop-blur-md p-4 rounded-full ring-4 ring-white/20 shadow-xl">
                        <svg class="w-16 h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="p-8 md:p-10">
                    <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2">Selamat Datang! 🎓</h2>
                    <p class="text-slate-500 dark:text-slate-400 text-lg leading-relaxed">
                        Selamat! Anda kini resmi menjadi bagian dari keluarga besar <strong>UNMARIS</strong>.
                    </p>

                    <!-- NIM Reveal -->
                    <div
                        class="my-8 bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-6 border border-slate-100 dark:border-slate-700">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Nomor Induk Mahasiswa
                            (NIM) Anda</p>
                        <p class="text-4xl font-black text-brand-blue dark:text-brand-gold tracking-wider select-all cursor-pointer"
                            onclick="navigator.clipboard.writeText('{{ $student->nim }}'); alert('NIM disalin!')">
                            {{ $student->nim }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-2">*Gunakan NIM ini untuk login selanjutnya (Password
                            tetap sama).</p>
                    </div>

                    <!-- Next Step -->
                    <div class="space-y-3">
                        <p class="text-sm font-bold text-slate-600 dark:text-slate-300">Langkah Selanjutnya:</p>
                        <button wire:click="dismissOnboarding"
                            class="w-full py-4 bg-brand-blue text-white rounded-xl font-bold text-lg shadow-xl shadow-blue-900/20 hover:bg-blue-800 hover:scale-[1.02] transition-all">
                            Mulai Isi KRS Sekarang &rarr;
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Script Confetti (Efek Pesta) -->
        <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
        <script>
            // Jalankan efek meledak saat modal muncul
            var duration = 3 * 1000;
            var animationEnd = Date.now() + duration;
            var defaults = {
                startVelocity: 30,
                spread: 360,
                ticks: 60,
                zIndex: 100
            };

            function randomInRange(min, max) {
                return Math.random() * (max - min) + min;
            }

            var interval = setInterval(function() {
                var timeLeft = animationEnd - Date.now();

                if (timeLeft <= 0) {
                    return clearInterval(interval);
                }

                var particleCount = 50 * (timeLeft / duration);
                // since particles fall down, start a bit higher than random
                confetti(Object.assign({}, defaults, {
                    particleCount,
                    origin: {
                        x: randomInRange(0.1, 0.3),
                        y: Math.random() - 0.2
                    }
                }));
                confetti(Object.assign({}, defaults, {
                    particleCount,
                    origin: {
                        x: randomInRange(0.7, 0.9),
                        y: Math.random() - 0.2
                    }
                }));
            }, 250);
        </script>
    @endif
    <!-- ERROR STATE -->
    @if (!$student)
        <div class="rounded-3xl border border-red-500/20 bg-red-500/10 p-8 text-center backdrop-blur-md">
            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-red-500/20 text-red-500 mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white">Akun Belum Terhubung</h3>
            <p class="text-slate-500 dark:text-slate-400 mt-2">Data akademik tidak ditemukan. Mohon hubungi BAAK.</p>
        </div>
    @else
        <!-- 1. HERO & STATS ROW -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- WELCOME CARD -->
            <div
                class="lg:col-span-8 relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-slate-900 p-6 md:p-8 text-white shadow-2xl group">
                <!-- Background FX -->
                <div
                    class="absolute right-0 top-0 h-full w-full bg-gradient-to-l from-brand-blue/80 to-transparent opacity-60">
                </div>
                <div
                    class="absolute -right-20 -top-40 h-96 w-96 rounded-full bg-brand-gold/10 blur-[80px] group-hover:bg-brand-gold/20 transition-all duration-700">
                </div>

                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center gap-6 md:gap-8">
                    <!-- Avatar -->
                    <div class="relative">
                        <div
                            class="h-20 w-20 md:h-24 md:w-24 rounded-3xl bg-gradient-to-br from-brand-gold to-orange-500 p-[3px] shadow-[0_0_30px_rgba(251,191,36,0.3)]">
                            <div
                                class="h-full w-full rounded-[20px] md:rounded-[22px] bg-slate-900 flex items-center justify-center text-3xl md:text-4xl font-extrabold text-brand-gold overflow-hidden">
                                @if ($student->photo)
                                    <img src="{{ asset('storage/' . $student->photo) }}"
                                        class="h-full w-full object-cover">
                                @else
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                @endif
                            </div>
                        </div>
                        <div
                            class="absolute -bottom-2 -right-2 rounded-xl border-4 border-slate-900 bg-green-500 px-2 py-0.5 md:px-3 md:py-1 text-[8px] md:text-[10px] font-black tracking-wider text-slate-900 shadow-sm uppercase">
                            {{ $student->status == 'A' ? 'AKTIF' : 'NON-AKTIF' }}
                        </div>
                    </div>

                    <div class="space-y-1 w-full">
                        <p
                            class="text-blue-200 font-bold tracking-widest text-[10px] md:text-xs uppercase mb-1 flex items-center gap-2">
                            <span class="h-1 w-6 md:w-8 bg-brand-gold rounded-full"></span>
                            {{ $greeting }}
                        </p>
                        <h1 class="text-2xl md:text-5xl font-black tracking-tight leading-tight truncate">
                            {{ explode(' ', Auth::user()->name)[0] }}<span class="text-brand-gold">.</span>
                        </h1>
                        <div
                            class="flex flex-wrap items-center gap-2 md:gap-3 text-xs md:text-sm text-slate-300 pt-2 font-medium">
                            <span
                                class="bg-white/10 px-2 py-1 md:px-3 rounded-full border border-white/5 font-mono tracking-wider">{{ $student->nim }}</span>
                            <span class="hidden md:inline text-slate-500">•</span>
                            <span
                                class="truncate max-w-[200px] md:max-w-none">{{ $student->study_program?->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- IPK CHART CARD (DINAMIS) -->
            <div
                class="lg:col-span-4 rounded-[2rem] md:rounded-[2.5rem] bg-white dark:bg-slate-800 p-6 shadow-sm border border-slate-100 dark:border-slate-700 relative overflow-hidden flex flex-col justify-center items-center">
                <h4
                    class="text-slate-500 dark:text-slate-400 font-bold text-xs tracking-widest uppercase absolute top-6 left-6">
                    Performa</h4>

                <!-- Chart -->
                <div id="gpaChart" class="-mt-4 scale-90 md:scale-100"></div>

                <div class="absolute bottom-6 text-center">
                    <p class="text-xs text-slate-400 font-medium">IPK KUMULATIF</p>
                    <!-- DATA DINAMIS IPK -->
                    <p class="text-2xl md:text-3xl font-black text-slate-800 dark:text-white">
                        {{ number_format($ipk, 2) }} <span class="text-sm md:text-base font-medium text-slate-400">/
                            4.00</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. MAIN BENTO GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- LEFT COL -->
            <div class="lg:col-span-2 space-y-6">

                <!-- JADWAL HARI INI -->
                <div
                    class="rounded-[2rem] md:rounded-[2.5rem] bg-white dark:bg-slate-800 p-6 md:p-8 shadow-sm border border-slate-100 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-6 md:mb-8">
                        <div>
                            <h3 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                                Today's Schedule</h3>
                            <p class="text-slate-500 font-medium mt-1 text-sm md:text-base">
                                {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                            </p>
                        </div>
                        @if ($jadwal_hari_ini->isNotEmpty())
                            <div
                                class="inline-flex items-center gap-2 rounded-full bg-brand-blue/10 px-3 py-1.5 md:px-4 md:py-2 text-[10px] md:text-xs font-extrabold text-brand-blue dark:bg-brand-blue dark:text-white">
                                <span class="relative flex h-2 w-2 md:h-2.5 md:w-2.5">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span
                                        class="relative inline-flex rounded-full h-2 w-2 md:h-2.5 md:w-2.5 bg-blue-500 dark:bg-white"></span>
                                </span>
                                LIVE
                            </div>
                        @endif
                    </div>

                    @if ($jadwal_hari_ini->isEmpty())
                        <div
                            class="flex flex-col items-center justify-center py-8 md:py-12 text-center rounded-3xl bg-slate-50 dark:bg-slate-900/50 border-2 border-dashed border-slate-200 dark:border-slate-700">
                            <div
                                class="h-14 w-14 md:h-16 md:w-16 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-4 dark:bg-slate-800 dark:text-indigo-400 shadow-sm">
                                <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="text-base md:text-lg font-bold text-slate-800 dark:text-white">No Classes Today
                            </h4>
                            <p class="text-slate-500 font-medium text-xs md:text-sm mt-1">Waktunya istirahat atau nugas!
                            </p>
                        </div>
                    @else
                        <div
                            class="relative space-y-6 pl-6 border-l-2 border-slate-100 dark:border-slate-700 ml-2 md:ml-4">
                            @foreach ($jadwal_hari_ini as $sch)
                                <div class="relative group">
                                    <div
                                        class="absolute -left-[29px] md:-left-[31px] top-6 h-3 w-3 md:h-4 md:w-4 rounded-full border-[3px] border-white bg-brand-blue shadow-md dark:border-slate-800 transition-all group-hover:scale-125">
                                    </div>

                                    <div
                                        class="rounded-3xl bg-slate-50 p-4 md:p-5 transition-all hover:-translate-y-1 hover:shadow-lg dark:bg-slate-700/30 group-hover:bg-brand-blue/5 dark:group-hover:bg-brand-blue/10">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <span
                                                        class="inline-block rounded-lg bg-white px-2 py-1 md:px-3 text-[10px] md:text-xs font-extrabold text-slate-700 shadow-sm dark:bg-slate-800 dark:text-white">
                                                        {{ \Carbon\Carbon::parse($sch->start_time)->format('H:i') }} -
                                                        {{ \Carbon\Carbon::parse($sch->end_time)->format('H:i') }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wide flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        {{ $sch->room_name }}
                                                    </span>
                                                </div>
                                                <h4
                                                    class="text-lg md:text-xl font-black text-slate-800 dark:text-white group-hover:text-brand-blue transition-colors">
                                                    {{ $sch->course_name }}
                                                </h4>
                                                <p
                                                    class="text-xs md:text-sm font-medium text-slate-500 mt-1 flex items-center gap-2">
                                                    <span
                                                        class="h-5 w-5 md:h-6 md:w-6 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center text-[10px]">👨‍🏫</span>
                                                    {{ $sch->lecturer_name }}
                                                </p>
                                            </div>
                                            <div class="shrink-0">
                                                <div
                                                    class="text-center rounded-2xl bg-white px-3 py-2 md:px-4 md:py-3 shadow-sm border border-slate-100 dark:bg-slate-800 dark:border-slate-600">
                                                    <p
                                                        class="text-[8px] md:text-[10px] text-slate-400 font-bold uppercase">
                                                        Kelas</p>
                                                    <p
                                                        class="text-base md:text-lg font-black text-brand-blue dark:text-brand-gold">
                                                        {{ $sch->class_name }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- SKS PROGRESS CHART (DINAMIS SESUAI JENJANG) -->
                <div
                    class="rounded-[2rem] md:rounded-[2.5rem] bg-brand-blue text-white p-6 md:p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 h-60 w-60 rounded-full bg-brand-gold/20 blur-[60px]">
                    </div>

                    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                        <div class="flex-1">
                            <h3 class="text-lg md:text-xl font-black">Progress Studi</h3>
                            <p class="text-blue-200 text-xs md:text-sm mt-1 font-medium">
                                Jenjang {{ $student->study_program->degree ?? 'S1' }} (Min. {{ $target_sks }} SKS)
                            </p>

                            <div class="mt-4 md:mt-6">
                                <!-- Data SKS Kumulatif vs Target -->
                                <div class="flex items-end gap-2 mb-2">
                                    <p class="text-3xl md:text-4xl font-black text-brand-gold tracking-tight">
                                        {{ $total_sks_kumulatif }}
                                    </p>
                                    <span class="text-base md:text-lg text-white font-bold opacity-60 mb-1">/
                                        {{ $target_sks }} SKS</span>
                                </div>

                                <!-- Custom Progress Bar -->
                                @php
                                    $percent = ($total_sks_kumulatif / $target_sks) * 100;
                                    $percent = $percent > 100 ? 100 : $percent;
                                @endphp
                                <div class="w-full bg-black/20 rounded-full h-3 overflow-hidden backdrop-blur-sm">
                                    <div class="bg-brand-gold h-full rounded-full shadow-[0_0_10px_rgba(255,215,0,0.5)] transition-all duration-1000 ease-out"
                                        style="width: {{ $percent }}%"></div>
                                </div>
                                <p class="mt-2 text-[10px] text-blue-200 font-mono">
                                    {{ number_format($percent, 1) }}% Menuju Kelulusan
                                </p>
                            </div>
                        </div>

                        <!-- Bar Chart Sejarah SKS -->
                        <div class="w-full md:w-1/2 h-24 md:h-32" id="sksChart"></div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COL -->
            <div class="space-y-6">

                <!-- QUICK ACTIONS -->
                <div class="grid grid-cols-1 gap-4">
                    <a href="{{ route('student.krs') }}"
                        class="group relative overflow-hidden rounded-[2rem] bg-[#0F172A] p-6 text-center text-white shadow-lg transition-all hover:scale-[1.02] hover:shadow-xl">
                        <div
                            class="absolute inset-0 bg-brand-blue opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                        </div>
                        <div class="relative z-10 flex items-center justify-between">
                            <div class="text-left">
                                <p class="font-black text-lg">KRS Online</p>
                                <p class="text-xs text-slate-400 group-hover:text-blue-200">Ambil matkul semester ini
                                </p>
                            </div>
                            <div
                                class="h-10 w-10 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                <svg class="h-5 w-5 text-brand-gold" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                        </div>
                    </a>

                    <a href="#"
                        class="group relative overflow-hidden rounded-[2rem] bg-white p-6 text-center shadow-sm border border-slate-100 transition-all hover:scale-[1.02] hover:border-brand-gold hover:shadow-lg dark:bg-slate-800 dark:border-slate-700">
                        <div class="relative z-10 flex items-center justify-between">
                            <div class="text-left">
                                <p class="font-black text-lg text-slate-800 dark:text-white">Cetak KHS</p>
                                <p class="text-xs text-slate-500">Lihat hasil studi</p>
                            </div>
                            <div
                                class="h-10 w-10 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-brand-gold/10 transition-colors dark:bg-slate-700">
                                <svg class="h-5 w-5 text-slate-400 group-hover:text-brand-gold" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- FINANCE WIDGET (DINAMIS) -->
                <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 p-6 text-white shadow-lg flex items-center justify-between group cursor-pointer hover:scale-[1.01] transition-transform"
                    onclick="window.location='{{ route('student.bills') }}'">

                    @if ($tagihan_belum_bayar > 0)
                        <!-- JIKA ADA HUTANG (MERAH) -->
                        <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-orange-600 opacity-90"></div>
                        <div
                            class="absolute -right-10 -bottom-10 h-40 w-40 rounded-full bg-white/20 blur-2xl group-hover:bg-white/30 transition-all animate-pulse">
                        </div>

                        <div class="relative z-10 flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-red-100 uppercase tracking-wider">Tagihan Belum Lunas
                                </p>
                                <p class="text-lg font-black">Rp
                                    {{ number_format($tagihan_belum_bayar, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @else
                        <!-- JIKA LUNAS (HIJAU) -->
                        <div class="absolute inset-0 bg-gradient-to-r from-green-600 to-teal-600 opacity-90"></div>
                        <div
                            class="absolute -right-10 -bottom-10 h-40 w-40 rounded-full bg-white/20 blur-2xl group-hover:bg-white/30 transition-all">
                        </div>

                        <div class="relative z-10 flex items-center gap-4">
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-green-100 uppercase tracking-wider">Status Keuangan
                                </p>
                                <p class="text-lg font-black">SPP LUNAS</p>
                            </div>
                        </div>
                    @endif

                    <div class="relative z-10">
                        <svg class="h-6 w-6 text-white/50 group-hover:text-white group-hover:translate-x-1 transition-all"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>

<!-- Script Chart Modern -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:navigated', () => {

        // Ambil Data Dinamis dari Controller
        const ipk = @json($ipk);
        const sksValues = @json($sks_history_values);
        const sksLabels = @json($sks_history_labels);

        // 1. SKS CHART (Bar Chart Minimalis)
        var optionsSKS = {
            series: [{
                name: 'SKS',
                data: sksValues.length > 0 ? sksValues : [0]
            }],
            chart: {
                type: 'bar',
                height: 100,
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%'
                }
            },
            colors: ['#ffffff'], // Putih karena background biru
            tooltip: {
                theme: 'dark',
                fixed: {
                    enabled: false
                },
                x: {
                    show: false
                }
            },
        };
        var chartSKS = new ApexCharts(document.querySelector("#sksChart"), optionsSKS);
        chartSKS.render();

        // 2. IPK CHART (Radial Bar)
        var percentage = (ipk / 4.0) * 100;

        var optionsIPK = {
            series: [percentage],
            chart: {
                height: 140,
                type: 'radialBar',
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            plotOptions: {
                radialBar: {
                    hollow: {
                        size: '55%'
                    },
                    track: {
                        background: '#f1f5f9',
                        strokeWidth: '100%'
                    },
                    dataLabels: {
                        name: {
                            show: false
                        },
                        value: {
                            fontSize: '16px',
                            fontWeight: 900,
                            color: '#334155',
                            offsetY: 6,
                            formatter: function(val) {
                                return ipk.toFixed(2);
                            }
                        }
                    }
                }
            },
            colors: ['#FFD700'], // Brand Gold
            stroke: {
                lineCap: 'round'
            },
        };
        var chartIPK = new ApexCharts(document.querySelector("#gpaChart"), optionsIPK);
        chartIPK.render();
    });
</script>
