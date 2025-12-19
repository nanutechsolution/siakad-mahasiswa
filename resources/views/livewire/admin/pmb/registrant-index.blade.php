<div class="space-y-6">
    <x-slot name="header">Aktivasi Mahasiswa Baru (Lulus Seleksi)</x-slot>

    <!-- 1. TOOLBAR PINTAR -->
    <div class="flex flex-col lg:flex-row gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 items-center justify-between">
        
        <!-- Search Input -->
        <div class="flex-1 w-full">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / No. Pendaftaran..."
                    class="pl-10 w-full rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 focus:ring-brand-blue focus:border-brand-blue transition-all">
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto">
            <!-- Filter Prodi -->
            <select wire:model.live="filter_prodi" class="w-full sm:w-48 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 text-sm focus:ring-brand-blue cursor-pointer">
                <option value="">Semua Prodi</option>
                @foreach ($prodis as $prodi)
                    <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                @endforeach
            </select>

            <!-- Filter Status Pintar (Stage) -->
            <select wire:model.live="filter_stage" class="w-full sm:w-64 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 text-sm font-bold focus:ring-brand-blue cursor-pointer bg-slate-50 dark:bg-slate-800">
                <option value="">📂 Semua Tahapan</option>
                <option value="unpaid">🟠 Belum Bayar</option>
                <option value="ready">🟢 Siap Generate NIM (Bayar OK)</option>
                <option value="active">🟣 Sudah Aktif (Punya NIM)</option>
            </select>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 text-green-700 rounded-xl font-bold border border-green-200 shadow-sm flex items-center gap-2 animate-fade-in">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 bg-red-50 text-red-700 rounded-xl font-bold border border-red-200 shadow-sm flex items-center gap-2 animate-pulse">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- 2. TABEL DATA -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest border-b border-slate-100 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-4">Calon Mahasiswa</th>
                    <th class="px-6 py-4">Prodi Diterima</th>
                    <th class="px-6 py-4">Status Administrasi</th> <!-- Kolom Cerdas -->
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach ($registrants as $reg)
                    @php
                        // LOGIC DETEKSI STATUS DI BLADE (Updated)
                        $isStudent = $reg->user->role === 'student';
                        $billing = $reg->billing;
                        $billingStatus = $billing?->status ?? 'UNPAID';
                        $isPaid = in_array($billingStatus, ['PAID', 'PARTIAL']);
                        
                        // Perbaikan: Cek apakah ada pembayaran PENDING di dalam billing
                        // Menggunakan collection method 'contains' pada relasi payments
                        $hasPendingPayment = $billing && $billing->payments->contains('status', 'PENDING');
                    @endphp
                    
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full {{ $isStudent ? 'bg-purple-100 text-purple-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center font-bold text-xs shadow-sm">
                                    {{ substr($reg->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">{{ $reg->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono tracking-wide">{{ $reg->registration_no }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $reg->firstChoice->name }}</div>
                            <div class="text-[10px] text-slate-400">{{ $reg->firstChoice->faculty->code ?? '-' }}</div>
                        </td>
                        
                        <!-- KOLOM STATUS PINTAR -->
                        <td class="px-6 py-4">
                            @if ($isStudent)
                                {{-- KONDISI 4: SUDAH JADI MAHASISWA --}}
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-black uppercase bg-purple-50 text-purple-700 border border-purple-200">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        AKTIF
                                    </span>
                                    <div class="text-[10px] font-mono text-purple-600 bg-purple-50 px-2 py-0.5 rounded border border-purple-100 font-bold">
                                        {{ $reg->user->username }}
                                    </div>
                                </div>
                            
                            @elseif ($isPaid)
                                {{-- KONDISI 3: SUDAH BAYAR, SIAP NIM --}}
                                <div class="flex flex-col items-start gap-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-green-50 text-green-600 border border-green-200 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        SIAP GENERATE NIM
                                    </span>
                                    <span class="text-[10px] text-slate-400 ml-1">Pembayaran Terverifikasi</span>
                                </div>

                            @elseif ($hasPendingPayment)
                                {{-- KONDISI 2: MENUNGGU VERIFIKASI (LOGIKA BARU) --}}
                                <div class="flex flex-col items-start gap-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-yellow-50 text-yellow-600 border border-yellow-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-400 animate-ping"></span>
                                        MENUNGGU VERIFIKASI
                                    </span>
                                    <span class="text-[10px] text-slate-400 ml-1">Bukti Terupload</span>
                                </div>

                            @else
                                {{-- KONDISI 1: BELUM BAYAR --}}
                                <div class="flex flex-col items-start gap-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-orange-50 text-orange-600 border border-orange-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                                        MENUNGGU PEMBAYARAN
                                    </span>
                                </div>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <button wire:click="showDetail('{{ $reg->id }}')"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm border group-hover:shadow-md
                                {{ $isStudent 
                                    ? 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' 
                                    : ($isPaid 
                                        ? 'bg-brand-blue text-white border-brand-blue hover:bg-blue-700 shadow-blue-500/30 transform hover:scale-105' 
                                        : 'bg-white text-slate-400 border-slate-200 hover:text-slate-600') 
                                }}">
                                @if($isStudent)
                                    Lihat Data
                                @elseif($isPaid)
                                    🚀 PROSES NIM
                                @elseif($hasPendingPayment)
                                    ⏳ Cek Validasi
                                @else
                                    Cek Tagihan
                                @endif
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Empty State -->
        @if($registrants->count() == 0)
            <div class="p-12 text-center text-slate-400">
                <p>Tidak ada data mahasiswa ditemukan.</p>
            </div>
        @endif

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-50 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800">
            {{ $registrants->links() }}
        </div>
    </div>

    <!-- 3. MODAL DETAIL -->
    @if ($isModalOpen && $selectedRegistrant)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90">
            
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden relative">
                
                <!-- Header Modal -->
                <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-start bg-slate-50/50 dark:bg-slate-700/50">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            @if($selectedRegistrant->user->role == 'student')
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-purple-600 text-white uppercase shadow-sm">SUDAH AKTIF</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-brand-blue text-white uppercase shadow-sm">LULUS SELEKSI</span>
                            @endif
                            <span class="text-xs text-slate-400 font-mono">{{ $selectedRegistrant->registration_no }}</span>
                        </div>
                        <h3 class="font-black text-xl text-slate-900 dark:text-white">{{ $selectedRegistrant->user->name }}</h3>
                        
                        @if($selectedRegistrant->user->role == 'student')
                            <p class="text-sm font-bold text-purple-600 mt-1 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                                NIM: {{ $selectedRegistrant->user->username }}
                            </p>
                        @else
                            <p class="text-sm text-slate-500">Prodi: {{ $selectedRegistrant->firstChoice->name }}</p>
                        @endif
                    </div>
                    <button wire:click="$set('isModalOpen', false)" class="p-2 hover:bg-red-50 hover:text-red-500 rounded-full transition-colors text-slate-400">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="p-8 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                    
                    <!-- MONITORING CICILAN -->
                    @php
                        $isReady = $selectedBilling && in_array($selectedBilling->status, ['PAID', 'PARTIAL']);
                    @endphp

                    <div class="relative">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Keuangan Daftar Ulang
                            </h4>
                            @if($selectedBilling)
                                <span class="px-2 py-1 rounded text-[10px] font-bold border 
                                    {{ $selectedBilling->status == 'PAID' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-orange-50 text-orange-600 border-orange-200' }}">
                                    STATUS: {{ $selectedBilling->status }}
                                </span>
                            @endif
                        </div>

                        @if ($selectedBilling)
                            <div class="bg-slate-50 dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 shadow-inner">
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div>
                                        <p class="text-xs text-slate-500 mb-1">Total Tagihan</p>
                                        <p class="text-lg font-black text-slate-800 dark:text-white">Rp {{ number_format($selectedBilling->amount, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-slate-500 mb-1">Sudah Dibayar (Valid)</p>
                                        <p class="text-lg font-black text-green-600">Rp {{ number_format($total_paid, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-3 overflow-hidden mb-2">
                                    <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $payment_progress >= 100 ? 'bg-green-500' : 'bg-brand-blue' }}"
                                         style="width: {{ $payment_progress }}%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] font-bold uppercase text-slate-400">
                                    <span>{{ $payment_progress }}% Terbayar</span>
                                    <span>Sisa: <span class="text-red-500">Rp {{ number_format($remaining_balance, 0, ',', '.') }}</span></span>
                                </div>
                            </div>
                        @else
                            <div class="bg-red-50 border border-red-100 rounded-2xl p-6 text-center">
                                <p class="text-red-500 font-bold text-sm">⚠️ Data Tagihan Belum Ada</p>
                                <p class="text-xs text-red-400 mt-1">Mahasiswa ini belum memiliki tagihan daftar ulang.</p>
                            </div>
                        @endif
                    </div>

                    <!-- RIWAYAT TRANSAKSI -->
                    @if ($selectedBilling && $selectedBilling->payments->count() > 0)
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-3">Histori Pembayaran</h4>
                            <div class="border border-slate-100 dark:border-slate-700 rounded-xl overflow-hidden">
                                <table class="w-full text-xs text-left">
                                    <thead class="bg-slate-50 dark:bg-slate-900 text-slate-500 font-bold border-b border-slate-100 dark:border-slate-700">
                                        <tr>
                                            <th class="px-4 py-3">Tanggal</th>
                                            <th class="px-4 py-3">Nominal</th>
                                            <th class="px-4 py-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                        @foreach ($selectedBilling->payments as $pay)
                                            <tr>
                                                <td class="px-4 py-2 text-slate-500">{{ $pay->payment_date->format('d/m/Y') }}</td>
                                                <td class="px-4 py-2 font-bold">Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}</td>
                                                <td class="px-4 py-2 text-center">
                                                    @if ($pay->status == 'VERIFIED')
                                                        <span class="text-green-600 font-bold text-[10px] bg-green-50 px-2 py-0.5 rounded border border-green-100">LUNAS</span>
                                                    @elseif ($pay->status == 'PENDING')
                                                        <span class="text-orange-500 font-bold text-[10px] bg-orange-50 px-2 py-0.5 rounded border border-orange-100">MENUNGGU</span>
                                                    @else
                                                        <span class="text-red-500 font-bold text-[10px]">DITOLAK</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- FOOTER AKSI -->
                <div class="px-8 py-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700 flex justify-end items-center gap-4">
                    
                    @if($selectedRegistrant->user->role == 'student')
                        <div class="flex items-center gap-3 bg-purple-100 text-purple-800 px-6 py-3 rounded-xl border border-purple-200 w-full justify-center shadow-inner">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div class="text-left">
                                <div class="text-sm font-black uppercase">Mahasiswa Aktif</div>
                                <div class="text-xs opacity-80">Data sudah dipindahkan ke Master Mahasiswa</div>
                            </div>
                        </div>

                    @elseif ($isReady)
                        <button wire:click="reject" wire:confirm="Yakin membatalkan kelulusan?" class="text-red-400 font-bold text-xs hover:text-red-600 mr-auto uppercase tracking-wide">
                            Batalkan Kelulusan
                        </button>
                        
                        <div class="text-right hidden sm:block">
                            <p class="text-[10px] uppercase font-black text-green-600">Syarat Terpenuhi</p>
                            <p class="text-xs text-slate-500">Siap untuk aktivasi</p>
                        </div>
                        <button wire:click="promoteToStudent('{{ $selectedRegistrant->id }}')" 
                            wire:loading.attr="disabled"
                            class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-sm shadow-xl hover:bg-brand-blue transition-all transform hover:scale-105 flex items-center gap-2">
                            <span wire:loading.remove>GENERATE NIM & AKTIFKAN</span>
                            <span wire:loading>MEMPROSES...</span>
                        </button>
                    
                    @else
                        <button wire:click="reject" wire:confirm="Yakin membatalkan kelulusan?" class="text-red-400 font-bold text-xs hover:text-red-600 mr-auto uppercase tracking-wide">
                            Batalkan Kelulusan
                        </button>
                        <div class="flex items-center gap-2 text-orange-500 bg-orange-50 px-4 py-2 rounded-lg border border-orange-100">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-xs font-bold">Menunggu Pembayaran</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>