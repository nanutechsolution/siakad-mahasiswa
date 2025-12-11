<div class="bg-white dark:bg-slate-900 min-h-screen font-sans text-slate-600 dark:text-slate-300 selection:bg-brand-gold selection:text-white">
    
    <!-- NAVBAR -->
    {{-- <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" class="h-8 w-auto">
                    <div>
                        <h1 class="font-black text-slate-900 dark:text-white text-lg leading-none tracking-tight">PMB UNMARIS</h1>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-bold text-brand-blue dark:text-blue-400 hover:underline">Dashboard Saya</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white">Masuk</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-brand-blue text-white rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20">
                            Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav> --}}

    <!-- HERO SECTION -->
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[1000px] h-[500px] bg-brand-blue/5 rounded-[100%] blur-3xl -z-10 dark:bg-brand-blue/10"></div>
        
        <div class="max-w-4xl mx-auto text-center">
            @if($active_wave)
                <span class="inline-block py-1 px-3 rounded-full bg-green-100 text-green-700 text-xs font-black tracking-widest uppercase mb-6 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800 animate-pulse">
                    {{ $active_wave->name }} Dibuka
                </span>
            @else
                <span class="inline-block py-1 px-3 rounded-full bg-red-100 text-red-700 text-xs font-black tracking-widest uppercase mb-6 border border-red-200">
                    Pendaftaran Tutup
                </span>
            @endif

            <h1 class="text-5xl md:text-7xl font-black text-slate-900 dark:text-white tracking-tight mb-6 leading-tight">
                Mulai Masa Depanmu <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-blue to-indigo-600 dark:from-blue-400 dark:to-indigo-400">Di Sini.</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-500 dark:text-slate-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                Bergabunglah dengan ribuan mahasiswa berprestasi di Universitas Stella Maris Sumba. Kampus modern dengan kurikulum berbasis teknologi.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @if($active_wave)
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-brand-blue text-white rounded-xl font-bold text-lg hover:bg-blue-800 hover:scale-105 transition-all shadow-xl shadow-blue-900/20">
                        Daftar Sekarang &rarr;
                    </a>
                @else
                    <button disabled class="px-8 py-4 bg-slate-200 text-slate-400 rounded-xl font-bold text-lg cursor-not-allowed">
                        Pendaftaran Belum Dibuka
                    </button>
                @endif
                <a href="#prodi" class="px-8 py-4 bg-white text-slate-700 border border-slate-200 rounded-xl font-bold text-lg hover:bg-slate-50 hover:border-slate-300 transition-all dark:bg-slate-800 dark:text-white dark:border-slate-700 dark:hover:bg-slate-700">
                    Lihat Jurusan
                </a>
            </div>
        </div>
    </section>

    <!-- JALUR MASUK -->
    <section class="py-20 bg-white border-y border-slate-100 dark:bg-slate-800/50 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 hover:border-brand-blue/30 transition-colors">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl mb-6">🎓</div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Jalur Reguler</h3>
                    <p class="text-slate-500">Seleksi berdasarkan nilai rapor atau ujian tulis komputer (CBT). Terbuka untuk semua lulusan SMA/SMK.</p>
                </div>
                <!-- Card 2 -->
                <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 hover:border-brand-blue/30 transition-colors">
                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center text-2xl mb-6">🏆</div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Jalur Prestasi</h3>
                    <p class="text-slate-500">Masuk tanpa tes bagi siswa berprestasi akademik (Ranking 1-10) atau Non-Akademik (Olahraga/Seni).</p>
                </div>
                <!-- Card 3 -->
                <div class="p-8 rounded-3xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 hover:border-brand-blue/30 transition-colors">
                    <div class="w-12 h-12 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center text-2xl mb-6">💝</div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Beasiswa Yayasan</h3>
                    <p class="text-slate-500">Program bantuan biaya pendidikan bagi calon mahasiswa kurang mampu dengan potensi akademik tinggi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- PRODI LIST -->
    <section id="prodi" class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-black text-slate-900 dark:text-white">Program Studi Pilihan</h2>
                <p class="text-slate-500 mt-2">Pilih jurusan yang sesuai dengan minat dan bakatmu.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($prodis as $prodi)
                <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-800 p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:shadow-xl hover:border-brand-blue/50 transition-all">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="text-xs font-bold uppercase text-slate-400 tracking-widest">{{ $prodi->degree }}</span>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mt-1 group-hover:text-brand-blue transition-colors">{{ $prodi->name }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-400 group-hover:bg-brand-blue group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 line-clamp-2">
                        Program studi unggulan di bawah naungan {{ $prodi->faculty->name ?? 'Fakultas' }}.
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

</div>