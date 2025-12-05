<div class="max-w-4xl mx-auto pt-24 pb-20 px-6">
    
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-slate-900 dark:text-white">Status Pendaftaran</h2>
        <p class="text-slate-500 mt-2">Pantau proses seleksi penerimaan mahasiswa baru Anda di sini.</p>
    </div>

    <!-- CARD UTAMA -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-100 dark:border-slate-800 overflow-hidden">
        
        <!-- Header Status -->
        <div class="p-10 text-center border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-4">Status Saat Ini</p>
            
            @php
                $status = $registrant->status;
            @endphp

            @if($status == \App\Enums\RegistrantStatus::SUBMITTED)
                <!-- ... (Kode Submitted Lama) ... -->
                 <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 font-bold text-sm shadow-sm border border-yellow-200 dark:border-yellow-800">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                    </span>
                    MENUNGGU VERIFIKASI
                </div>
                <p class="mt-6 text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                    Data Anda sedang diperiksa oleh Panitia PMB. Mohon tunggu 1-3 hari kerja.
                </p>

            @elseif($status == \App\Enums\RegistrantStatus::VERIFIED)
                 <!-- ... (Kode Verified Lama) ... -->
                 <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 font-bold text-sm shadow-sm border border-blue-200 dark:border-blue-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    BERKAS TERVERIFIKASI
                </div>
                <p class="mt-6 text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                    Selamat! Berkas Anda valid. Silakan cetak Kartu Peserta dan menunggu pengumuman kelulusan.
                </p>

            @elseif($status == \App\Enums\RegistrantStatus::ACCEPTED)
            <a href="{{ route('pmb.print.loa') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm font-bold hover:bg-blue-100 transition shadow-sm">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
        Download LoA
    </a>
                <!-- TAMPILAN BARU: LULUS / ACCEPTED -->
                <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 font-bold text-sm shadow-sm border border-green-200 dark:border-green-800 mb-6">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    LULUS SELEKSI
                </div>
                
                <div class="max-w-2xl mx-auto bg-white dark:bg-slate-800 rounded-2xl border-2 border-green-500/20 p-6 md:p-8 relative overflow-hidden">
                    <!-- Confetti BG -->
                    <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10"></div>
                    
                    <h3 class="text-3xl font-black text-slate-900 dark:text-white mb-2">SELAMAT! 🎓</h3>
                    <p class="text-slate-600 dark:text-slate-300 mb-6">
                        Anda dinyatakan <strong>DITERIMA</strong> sebagai calon mahasiswa Universitas Stella Maris Sumba pada Program Studi:
                    </p>
                    
                    <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-700 inline-block w-full md:w-auto min-w-[300px]">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Program Studi</p>
                        <p class="text-xl font-bold text-brand-blue dark:text-brand-gold">{{ $registrant->firstChoice->name }} ({{ $registrant->firstChoice->degree }})</p>
                    </div>

                    <!-- PANDUAN DAFTAR ULANG (Next Step) -->
                    <div class="mt-8 text-left space-y-4 border-t border-slate-100 dark:border-slate-700 pt-6">
                        <h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-blue text-white text-xs">!</span>
                            Langkah Selanjutnya: Daftar Ulang
                        </h4>
                        
                        <ol class="relative border-l-2 border-slate-200 dark:border-slate-700 ml-3 space-y-6">                  
                            <li class="ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-[13px] ring-4 ring-white dark:ring-slate-800 dark:bg-blue-900">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-300">1</span>
                                </span>
                                <h3 class="font-semibold text-slate-900 dark:text-white">Lakukan Pembayaran</h3>
                                <p class="text-sm text-slate-500 mt-1">Silakan transfer biaya <strong>Daftar Ulang / Uang Gedung</strong> sebesar:</p>
                                <div class="mt-2 p-3 bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-200 dark:border-yellow-800 rounded-lg text-center">
                                    <span class="text-2xl font-black text-slate-800 dark:text-white">Rp 5.000.000</span>
                                </div>
                            </li>
                            <li class="ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-[13px] ring-4 ring-white dark:ring-slate-800 dark:bg-blue-900">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-300">2</span>
                                </span>
                                <h3 class="font-semibold text-slate-900 dark:text-white">Transfer ke Rekening Kampus</h3>
                                @php $setting = \App\Models\Setting::first(); @endphp
                                <div class="mt-2 text-sm bg-slate-50 dark:bg-slate-900 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <p class="font-bold text-slate-700 dark:text-slate-300">{{ $setting->bank_name ?? 'Bank BRI' }}</p>
                                    <p class="font-mono text-lg font-black text-brand-blue dark:text-white tracking-wider">{{ $setting->bank_account ?? '0000-0000-0000' }}</p>
                                    <p class="text-xs text-slate-500">a.n. {{ $setting->bank_holder ?? 'Yayasan UNMARIS' }}</p>
                                </div>
                            </li>
                            <li class="ml-6">
                                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-[13px] ring-4 ring-white dark:ring-slate-800 dark:bg-blue-900">
                                    <span class="text-xs font-bold text-blue-600 dark:text-blue-300">3</span>
                                </span>
                                <h3 class="font-semibold text-slate-900 dark:text-white">Konfirmasi Pembayaran</h3>
                                <p class="text-sm text-slate-500 mt-1">Kirimkan bukti transfer ke Bagian Keuangan untuk mendapatkan NIM.</p>
                                <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20{{ Auth::user()->name }}%20(No.%20{{ $registrant->registration_no }})%20ingin%20konfirmasi%20pembayaran%20daftar%20ulang." target="_blank" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    Konfirmasi via WhatsApp
                                </a>
                            </li>
                        </ol>
                    </div>
                </div>

            @elseif($status == \App\Enums\RegistrantStatus::REJECTED)
                <!-- ... (Kode Rejected Lama) ... -->
                 <div class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 font-bold text-sm shadow-sm border border-red-200 dark:border-red-800">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    TIDAK LULUS
                </div>
                <p class="mt-6 text-sm text-slate-600 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                    Mohon maaf, berkas atau hasil seleksi Anda belum memenuhi kriteria penerimaan. Tetap semangat!
                </p>
            @endif
        </div>

        <!-- Footer Actions -->
        <div class="bg-slate-50 dark:bg-slate-800/50 px-8 py-6 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-end gap-3">
            
            @if($status != \App\Enums\RegistrantStatus::REJECTED && $status != \App\Enums\RegistrantStatus::DRAFT)
                <a href="{{ route('pmb.print.card') }}" target="_blank" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm hover:text-brand-blue">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak Kartu Peserta
                </a>
            @endif

            {{-- 
                NOTE: Tombol Lanjut Daftar Ulang sudah kita ganti dengan PANDUAN di atas 
                agar lebih jelas langkahnya. Tapi jika ingin tetap ada tombol ke WA, bisa ditaruh sini.
            --}}
        </div>
    </div>
</div>