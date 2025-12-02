<div class="mx-auto max-w-4xl space-y-8 font-sans">
    <x-slot name="header">Keuangan & Tagihan</x-slot>

    <!-- Header Info -->
    <div class="rounded-[2.5rem] bg-slate-900 p-8 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-500/20 rounded-full blur-[80px] -mr-16 -mt-16"></div>
        <div class="relative z-10 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">Status Keuangan</h2>
                <p class="text-slate-400 mt-1">Rekapitulasi kewajiban pembayaran semester ini.</p>
            </div>
            <!-- ... (header kanan tetap) ... -->
        </div>
    </div>

    <!-- ... (Alert & List Tagihan tetap sama) ... -->
    <!-- List Tagihan -->
    <div class="space-y-4">
        @forelse($billings as $bill)
            @php
                $paid_amount = $bill->payments->where('status', 'VERIFIED')->sum('amount_paid');
                $remaining = $bill->amount - $paid_amount;
            @endphp

            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-100 dark:border-slate-700 hover:border-brand-blue transition-all">
                <div class="flex flex-col md:flex-row justify-between gap-6">
                    <!-- Info Kiri -->
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            @if($bill->status == 'PAID')
                                <span class="bg-green-100 text-green-700 text-[10px] font-bold px-3 py-1 rounded-full">LUNAS</span>
                            @elseif($bill->status == 'PARTIAL')
                                <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-3 py-1 rounded-full">CICILAN</span>
                            @else
                                <span class="bg-red-100 text-red-700 text-[10px] font-bold px-3 py-1 rounded-full">BELUM LUNAS</span>
                            @endif
                            <span class="text-xs text-slate-400 font-mono">{{ $bill->created_at->format('d M Y') }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white group-hover:text-brand-blue transition-colors">
                            {{ $bill->title }}
                        </h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $bill->description ?? '-' }}</p>
                        
                        <!-- Status Pembayaran Terakhir -->
                        @if($bill->payments->count() > 0)
                            <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-900 rounded-lg text-xs border border-slate-100 dark:border-slate-700">
                                <p class="font-bold text-slate-700 dark:text-slate-300 mb-1">Riwayat Pembayaran:</p>
                                @foreach($bill->payments as $payment)
                                    <div class="flex justify-between items-center py-1 border-b border-slate-200 dark:border-slate-800 last:border-0">
                                        <span>Rp {{ number_format($payment->amount_paid, 0, ',', '.') }} ({{ $payment->payment_date->format('d/m/Y') }})</span>
                                        @if($payment->status == 'VERIFIED')
                                            <span class="text-green-600 font-bold">Sah</span>
                                        @elseif($payment->status == 'REJECTED')
                                            <span class="text-red-600 font-bold">Ditolak</span>
                                        @else
                                            <span class="text-yellow-600 font-bold">Menunggu Verifikasi</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Info Kanan & Aksi -->
                    <div class="text-left md:text-right flex flex-col justify-between items-start md:items-end">
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase">Total Tagihan</p>
                            <p class="text-xl font-bold text-slate-600 dark:text-slate-300 line-through decoration-slate-400/50">
                                Rp {{ number_format($bill->amount, 0, ',', '.') }}
                            </p>
                            
                            <p class="text-xs text-red-500 font-bold uppercase mt-2">Sisa Pembayaran</p>
                            <p class="text-3xl font-black text-slate-800 dark:text-white">
                                Rp {{ number_format($remaining, 0, ',', '.') }}
                            </p>

                            <p class="text-xs text-slate-400 mt-2">Jatuh Tempo: {{ $bill->due_date->format('d M Y') }}</p>
                        </div>

                        @if($remaining > 0)
                            <button wire:click="pay('{{ $bill->id }}')" 
                                    class="mt-4 w-full md:w-auto px-6 py-2.5 bg-slate-900 text-white rounded-xl font-bold hover:bg-brand-blue transition-colors shadow-lg shadow-slate-900/20">
                                Bayar / Upload Bukti
                            </button>
                        @else
                            <div class="mt-4 flex items-center gap-2 text-green-600 font-bold text-sm bg-green-50 px-4 py-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Lunas</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                <p class="text-slate-500 font-medium">Tidak ada tagihan aktif saat ini.</p>
            </div>
        @endforelse
    </div>

    <!-- MODAL PEMBAYARAN DENGAN PERINGATAN -->
    @if($isModalOpen && $selectedBilling)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Konfirmasi Pembayaran</h3>
                <p class="text-sm text-slate-500">{{ $selectedBilling->title }}</p>
            </div>
            
            <form wire:submit.prevent="storePayment" class="p-6 space-y-5">
                
                <!-- PERINGATAN ANTI-FRAUD (BARU) -->
                <div class="flex gap-3 p-4 bg-red-50 border border-red-100 rounded-xl items-start">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    <div class="text-xs text-red-700">
                        <span class="font-bold block mb-1">PERINGATAN KERAS!</span>
                        Dilarang memanipulasi/mengedit bukti transfer. Bukti akan divalidasi dengan Mutasi Bank. <br>
                        Pemalsuan dokumen adalah <strong>Pelanggaran Akademik Berat</strong> dan dapat dikenakan sanksi DO (Drop Out).
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Jumlah yang Dibayar</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-500 font-bold">Rp</span>
                        <input wire:model="amount_paid" type="number" class="w-full pl-10 rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white font-bold">
                    </div>
                    @error('amount_paid') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Transfer</label>
                    <input wire:model="payment_date" type="date" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    @error('payment_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Bukti Transfer (Asli)</label>
                    <input wire:model="proof_file" type="file" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-blue file:text-white hover:file:bg-blue-800">
                    @error('proof_file') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 font-bold">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-bold shadow-lg shadow-green-500/30">
                        <span wire:loading.remove wire:target="proof_file">Kirim Bukti</span>
                        <span wire:loading wire:target="proof_file">Uploading...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>