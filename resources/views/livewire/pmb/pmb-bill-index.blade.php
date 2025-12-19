<div class="max-w-4xl mx-auto px-6 font-sans">

<div class="mb-8">
    <h2 class="text-3xl font-black text-slate-900 dark:text-white">Pembayaran Daftar Ulang</h2>
    <p class="text-slate-500">Selesaikan administrasi Anda untuk mendapatkan NIM dan status Mahasiswa Aktif.</p>
</div>

@if(!$billing)
    <div class="bg-blue-50 border border-blue-200 p-8 rounded-[2rem] text-center">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-600 mb-4 text-2xl">⏳</div>
        <h3 class="text-lg font-bold text-blue-900">Tagihan Belum Diterbitkan</h3>
        <p class="text-blue-700 text-sm max-w-md mx-auto">Tagihan daftar ulang akan muncul setelah Anda dinyatakan LULUS SELEKSI oleh tim PMB.</p>
    </div>
@else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- INFO TAGIHAN & PEMBAYARAN -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Card Utama -->
            <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-700 p-8 relative overflow-hidden">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $billing->title }}</p>
                        <h3 class="text-4xl font-black text-slate-900 dark:text-white mt-1">
                            Rp {{ number_format($billing->amount, 0, ',', '.') }}
                        </h3>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border
                        {{ match($billing->status) {
                            'PAID' => 'bg-green-100 text-green-700 border-green-200',
                            'PARTIAL' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                            default => 'bg-red-100 text-red-700 border-red-200',
                        } }}">
                        {{ $billing->status == 'UNPAID' ? 'BELUM BAYAR' : ($billing->status == 'PARTIAL' ? 'TERBAYAR SEBAGIAN' : 'LUNAS') }}
                    </span>
                </div>

                @php
                    $totalPaid = $billing->payments->where('status', 'VERIFIED')->sum('amount_paid');
                    $remaining = $billing->amount - $totalPaid;
                @endphp

                <div class="grid grid-cols-2 gap-4 py-4 border-y border-slate-100 dark:border-slate-700 my-6 text-sm">
                    <div>
                        <p class="text-slate-400">Sudah Dibayar</p>
                        <p class="font-bold text-green-600">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-slate-400">Sisa Kewajiban</p>
                        <p class="font-bold text-red-500">Rp {{ number_format($remaining, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if($remaining > 0)
                    <button wire:click="$set('isModalOpen', true)" class="w-full py-4 bg-brand-blue text-white rounded-2xl font-bold shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        Upload Bukti Bayar (Lunas/Cicil)
                    </button>
                @else
                    <div class="bg-green-50 text-green-700 p-4 rounded-2xl border border-green-100 font-bold text-center">
                        ✅ Pembayaran Selesai. Silakan tunggu aktivasi NIM oleh Admin.
                    </div>
                @endif
            </div>

            <!-- Riwayat Upload -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-50 dark:border-slate-700 font-bold text-slate-800 dark:text-white">Riwayat Konfirmasi</div>
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($billing->payments as $pay)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-bold">Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-slate-400 uppercase">{{ $pay->payment_date->format('d M Y') }}</p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($pay->status == 'VERIFIED')
                                    <span class="text-green-600 font-bold text-xs">Diterima</span>
                                @elseif($pay->status == 'REJECTED')
                                    <span class="text-red-500 font-bold text-xs">Ditolak</span>
                                @else
                                    <span class="text-yellow-600 font-bold text-xs animate-pulse">Menunggu</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td class="px-6 py-8 text-center text-slate-400">Belum ada data konfirmasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- INSTRUKSI PEMBAYARAN -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-slate-900 rounded-[2rem] p-6 text-white shadow-xl">
                <h4 class="font-bold text-sm uppercase tracking-widest text-brand-gold mb-4">Metode Transfer</h4>
                
                <div class="p-4 bg-white/10 rounded-2xl border border-white/10 mb-6">
                    <p class="text-[10px] uppercase font-bold text-slate-400">{{ $settings->bank_name ?? 'Bank BRI' }}</p>
                    <p class="text-xl font-mono font-black tracking-widest my-1">{{ $settings->bank_account ?? '1234-5678-90' }}</p>
                    <p class="text-xs font-bold text-slate-300">a.n {{ $settings->bank_holder ?? 'Yayasan Stella Maris' }}</p>
                </div>

                <div class="space-y-4 text-xs text-slate-400 leading-relaxed">
                    <p>1. Pembayaran dapat dilakukan secara <strong>Lunas</strong> atau <strong>Mencicil (Bertahap)</strong>.</p>
                    <p>2. Syarat mendapatkan NIM adalah minimal telah melakukan satu kali pembayaran (DP).</p>
                    <p>3. Simpan struk asli untuk ditukarkan dengan Kartu Mahasiswa saat OSPEK.</p>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- MODAL UPLOAD -->
@if($isModalOpen)
<div class="fixed inset-0 z-50 flex
