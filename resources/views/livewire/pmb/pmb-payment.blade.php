<div class="max-w-6xl mx-auto px-6 font-sans pb-12">

    <!-- HEADER -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center gap-4">
        <div class="flex-1">
            <h2 class="text-2xl font-black text-slate-900">Pembayaran Awal (Daftar Ulang)</h2>
            <p class="text-slate-500 text-sm mt-1">
                Lakukan pembayaran pertama untuk aktivasi <span class="font-bold text-slate-800">NIM</span>. 
                Cicilan/pelunasan selanjutnya dilakukan melalui <span class="font-bold text-brand-blue">Sistem Siakad</span>.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- KOLOM KIRI: INFO REKENING & RINGKASAN -->
        <div class="lg:col-span-1 space-y-6">

            <!-- DAFTAR REKENING (MULTI) -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-widest text-slate-400">Transfer Ke Salah Satu:</h3>
                
                @foreach($banks as $bank)
                <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded">
                                {{ $bank['bank'] }}
                            </span>
                            <!-- Tombol Copy -->
                            <button onclick="navigator.clipboard.writeText('{{ $bank['no_rek'] }}'); alert('No Rekening {{ $bank['bank'] }} Disalin!')" 
                                    class="text-slate-400 hover:text-blue-600 transition-colors p-1" title="Salin No Rek">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                        <div class="font-mono text-xl font-black text-slate-800 tracking-tight">
                            {{ $bank['no_rek'] }}
                        </div>
                        <div class="text-[10px] font-bold uppercase text-slate-400 mt-1">
                            A.N. {{ $bank['an'] }}
                        </div>
                    </div>
                    <!-- Dekorasi Background -->
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-slate-50 rounded-full group-hover:bg-blue-50 transition-colors"></div>
                </div>
                @endforeach
            </div>

            <!-- RINGKASAN TAGIHAN -->
            <div class="bg-slate-900 rounded-3xl p-6 text-white shadow-xl">
                <h3 class="font-bold text-slate-200 mb-4 text-sm border-b border-slate-700 pb-2">Status Keuangan</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400">Total Biaya</span>
                        <span class="font-bold">Rp {{ number_format($billing->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-green-400">Sudah Masuk</span>
                        <span class="font-bold text-green-400">Rp {{ number_format($total_verified, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-2 border-t border-slate-700 flex justify-between items-center">
                        <span class="text-xs font-bold uppercase text-red-400">Sisa (Via Siakad)</span>
                        <span class="text-xl font-black text-red-500">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: FORM & HISTORY -->
        <div class="lg:col-span-2 space-y-6">
            
            {{-- LOGIKA: Form hanya muncul jika BELUM ADA pembayaran yang diverifikasi --}}
            @if ($remaining > 0 && $total_verified == 0)
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl shadow-slate-200/50">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="p-3 bg-brand-blue/10 text-brand-blue rounded-xl">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Form Pembayaran Pertama</h3>
                            <p class="text-xs text-slate-500">Silakan transfer sesuai ketentuan untuk aktivasi akun.</p>
                        </div>
                    </div>

                    <form wire:submit.prevent="submitPayment" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            
                            <!-- INPUT NOMINAL (AUTO FORMAT) -->
                            <div x-data>
                                <div class="flex justify-between items-end mb-1">
                                    <label class="block text-sm font-bold text-slate-700">Jumlah Transfer (Rp)</label>
                                    <!-- Label Tahapan Otomatis -->
                                    <span class="text-[10px] font-bold uppercase px-2 py-1 bg-yellow-100 text-yellow-700 rounded border border-yellow-200">
                                        Wajib Bayar Awal
                                    </span>
                                </div>
                                <div class="relative">
                                    <span class="absolute left-4 top-3.5 font-bold text-slate-400">Rp</span>
                                    <!-- Script Input Masking -->
                                    <input wire:model="amount_paid" 
                                           type="text"
                                           x-on:input="$el.value = $el.value.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                           class="w-full pl-12 pr-4 py-3 rounded-xl border-slate-300 focus:ring-brand-blue font-mono font-bold text-lg text-slate-800"
                                           placeholder="0">
                                </div>
                                @error('amount_paid') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- TANGGAL -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Transfer</label>
                                <input wire:model="payment_date" type="date"
                                    class="w-full py-3 rounded-xl border-slate-300 focus:ring-brand-blue text-slate-800">
                                @error('payment_date') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- UPLOAD BUKTI -->
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Bukti Transfer (Foto/Screenshot)</label>
                            <div class="relative border-2 border-dashed border-slate-300 rounded-2xl p-6 text-center hover:bg-slate-50 transition-colors group">
                                <input wire:model="proof_file" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*">
                                
                                @if ($proof_file)
                                    <div class="text-sm text-green-600 font-bold flex flex-col items-center">
                                        <svg class="w-8 h-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        {{ $proof_file->getClientOriginalName() }}
                                    </div>
                                @else
                                    <div class="text-slate-400 group-hover:text-slate-500">
                                        <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm font-medium">Klik untuk upload bukti</span>
                                    </div>
                                @endif
                            </div>
                            @error('proof_file') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" wire:loading.attr="disabled"
                                class="px-8 py-3 bg-brand-blue hover:bg-blue-800 text-white rounded-xl font-bold shadow-lg shadow-blue-900/20 transition-all flex items-center gap-2">
                                <span wire:loading.remove>Kirim Konfirmasi</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- JIKA SUDAH ADA PEMBAYARAN MASUK (CICILAN PERTAMA SUDAH OK) --}}
            @if($total_verified > 0)
                <div class="bg-blue-50 border border-blue-100 rounded-3xl p-8 text-center shadow-sm">
                    <div class="inline-flex p-4 bg-white rounded-full text-blue-500 mb-4 shadow-sm">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Pembayaran Awal Diterima</h3>
                    <p class="text-slate-600 max-w-md mx-auto mb-6">
                        Terima kasih, pembayaran tahap pertama Anda telah kami terima. Untuk pelunasan sisa tagihan (Cicilan ke-2 dst), silakan akses melalui <strong>Siakad</strong> setelah NIM Anda aktif.
                    </p>
                    
                    @if($remaining > 0)
                        <div class="inline-block px-4 py-2 bg-white rounded-lg border border-blue-100 text-sm font-bold text-slate-600">
                            Sisa Tagihan: <span class="text-red-500">Rp {{ number_format($remaining, 0, ',', '.') }}</span> (Bayar di Siakad)
                        </div>
                    @else
                        <div class="inline-block px-4 py-2 bg-green-100 rounded-lg text-sm font-bold text-green-700">
                            Status: LUNAS
                        </div>
                    @endif
                </div>
            @endif

            <!-- TABEL RIWAYAT -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm mt-6">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-sm">Riwayat Pembayaran</h3>
                    <span class="text-[10px] text-slate-500 uppercase tracking-wider font-bold">Log Transaksi</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-slate-100">
                            @forelse($payments as $pay)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="font-bold text-slate-800">{{ $pay->payment_date->format('d M Y') }}</p>
                                        <p class="text-xs text-slate-500">Via Transfer</p>
                                    </td>
                                    <td class="px-6 py-4 font-mono font-bold text-slate-700">
                                        Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if ($pay->status == 'VERIFIED')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                DITERIMA
                                            </span>
                                        @elseif($pay->status == 'REJECTED')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200" title="{{ $pay->rejection_note }}">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                DITOLAK
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 border border-yellow-200 animate-pulse">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                MENUNGGU
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-10 text-center text-slate-400 italic bg-slate-50/50">
                                        Belum ada data pembayaran.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>