<div class="bg-slate-50 dark:bg-slate-900 min-h-screen font-sans pt-24 pb-20">
    
    <!-- HEADER -->
    <div class="max-w-7xl mx-auto px-6 lg:px-8 mb-12 text-center">
        <h1 class="text-4xl font-black text-slate-900 dark:text-white mb-4">Pusat Informasi PMB</h1>
        <p class="text-lg text-slate-500 max-w-2xl mx-auto">
            Temukan semua informasi mengenai jadwal pendaftaran, pengumuman terbaru, dan pertanyaan yang sering diajukan.
        </p>
    </div>

    <div class="max-w-6xl mx-auto px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- KOLOM KIRI: JADWAL & PENGUMUMAN -->
        <div class="lg:col-span-2 space-y-10">
            
            <!-- 1. JADWAL PENDAFTARAN -->
            <section>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="p-2 bg-blue-100 text-blue-600 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></span>
                    Jadwal Pendaftaran
                </h2>
                
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 font-bold uppercase text-xs">
                                <tr>
                                    <th class="px-6 py-4">Gelombang</th>
                                    <th class="px-6 py-4">Mulai</th>
                                    <th class="px-6 py-4">Selesai</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($waves as $wave)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">{{ $wave->name }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $wave->start_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $wave->end_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if(now()->between($wave->start_date, $wave->end_date))
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                <span class="relative flex h-2 w-2">
                                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                </span>
                                                Buka
                                            </span>
                                        @elseif(now()->lt($wave->start_date))
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">Segera</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Tutup</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">Belum ada jadwal gelombang aktif.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- 2. PENGUMUMAN -->
            <section>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="p-2 bg-orange-100 text-orange-600 rounded-lg"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg></span>
                    Pengumuman Terbaru
                </h2>

                <div class="space-y-4">
                    @forelse($announcements as $ann)
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 transition hover:border-brand-blue">
                        <div class="flex items-center gap-3 mb-2 text-xs">
                            <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded font-mono">{{ $ann->created_at->format('d M Y') }}</span>
                            <span class="text-brand-blue font-bold">Admin PMB</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $ann->title }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed mb-4">
                            {{ $ann->content }}
                        </p>
                        @if($ann->attachment)
                            <a href="{{ asset('storage/'.$ann->attachment) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-brand-blue hover:underline">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                Download Lampiran
                            </a>
                        @endif
                    </div>
                    @empty
                    <div class="p-8 text-center bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                        <p class="text-slate-400">Tidak ada pengumuman saat ini.</p>
                    </div>
                    @endforelse
                </div>
            </section>

        </div>

        <!-- KOLOM KANAN: FAQ & KONTAK -->
        <div class="space-y-8">
            
            <!-- FAQ (Accordion Sederhana dengan Alpine) -->
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-6">FAQ (Tanya Jawab)</h3>
                
                <div class="space-y-4" x-data="{ active: null }">
                    <!-- Item 1 -->
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-4">
                        <button @click="active = (active === 1 ? null : 1)" class="flex justify-between items-center w-full text-left font-bold text-slate-700 dark:text-slate-300 text-sm hover:text-brand-blue transition">
                            <span>Bagaimana cara mendaftar?</span>
                            <svg class="w-4 h-4 transform transition-transform" :class="active === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="active === 1" x-collapse class="mt-2 text-sm text-slate-500 leading-relaxed">
                            Buat akun terlebih dahulu, lalu login dan isi formulir pendaftaran secara lengkap hingga tahap finalisasi.
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="border-b border-slate-100 dark:border-slate-700 pb-4">
                        <button @click="active = (active === 2 ? null : 2)" class="flex justify-between items-center w-full text-left font-bold text-slate-700 dark:text-slate-300 text-sm hover:text-brand-blue transition">
                            <span>Berapa biaya pendaftaran?</span>
                            <svg class="w-4 h-4 transform transition-transform" :class="active === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="active === 2" x-collapse class="mt-2 text-sm text-slate-500 leading-relaxed">
                            Biaya pendaftaran adalah Rp 250.000 untuk Gelombang 1 dan Rp 300.000 untuk Gelombang 2.
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="pb-2">
                        <button @click="active = (active === 3 ? null : 3)" class="flex justify-between items-center w-full text-left font-bold text-slate-700 dark:text-slate-300 text-sm hover:text-brand-blue transition">
                            <span>Kapan pengumuman kelulusan?</span>
                            <svg class="w-4 h-4 transform transition-transform" :class="active === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>
                        <div x-show="active === 3" x-collapse class="mt-2 text-sm text-slate-500 leading-relaxed">
                            Pengumuman biasanya dirilis 3 hari setelah ujian seleksi selesai. Cek menu "Status Pendaftaran" secara berkala.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontak -->
            <div class="bg-brand-blue text-white p-6 rounded-2xl shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-6 -mt-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                
                <h3 class="font-bold text-lg mb-4 relative z-10">Butuh Bantuan?</h3>
                <p class="text-blue-100 text-sm mb-6 relative z-10">
                    Jika mengalami kendala teknis atau pertanyaan lain, hubungi panitia.
                </p>
                
                <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center justify-center gap-2 w-full py-3 bg-white text-brand-blue rounded-xl font-bold text-sm hover:bg-blue-50 transition relative z-10">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Chat WhatsApp
                </a>
            </div>

        </div>
    </div>
</div>