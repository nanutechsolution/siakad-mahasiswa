<div class="space-y-6">
<x-slot name="header">Verifikasi Pembayaran</x-slot>

<!-- Toolbar -->
<div class="flex flex-col md:flex-row gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama Pembayar..." class="flex-1 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white">
    <select wire:model.live="filter_status" class="rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm">
        <option value="PENDING">Menunggu Verifikasi</option>
        <option value="VERIFIED">Sudah Diverifikasi</option>
        <option value="REJECTED">Ditolak</option>
        <option value="">Semua</option>
    </select>
</div>

@if (session()->has('message'))
    <div class="p-4 bg-green-100 text-green-700 rounded-xl font-bold border border-green-200 shadow-sm flex items-center gap-2">✅ {{ session('message') }}</div>
@endif

<!-- Tabel Pembayaran -->
<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest border-b border-slate-100 dark:border-slate-700">
            <tr>
                <th class="px-6 py-4">Pembayar</th>
                <th class="px-6 py-4">Tagihan</th>
                <th class="px-6 py-4">Nominal Bayar</th>
                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
            @foreach($payments as $pay)
            @php
                $payer = $pay->billing->registrant->user->name ?? $pay->billing->student->user->name ?? 'User Tak Dikenal';
                $type = $pay->billing->registrant_id ? 'CAMABA' : 'MAHASISWA';
            @endphp
            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                <td class="px-6 py-4">
                    <div class="font-bold text-slate-900 dark:text-white">{{ $payer }}</div>
                    <div class="text-[10px] text-brand-blue font-bold tracking-widest uppercase">{{ $type }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-xs text-slate-600 dark:text-slate-400 font-medium">{{ $pay->billing->title }}</div>
                    <div class="text-[10px] text-slate-400">Total Tagihan: Rp {{ number_format($pay->billing->amount, 0, ',', '.') }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-mono font-bold text-slate-900 dark:text-white">Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}</div>
                    <div class="text-[10px] text-slate-400">{{ $pay->payment_date->format('d M Y') }}</div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase
                        {{ match($pay->status) {
                            'VERIFIED' => 'bg-green-100 text-green-700',
                            'PENDING' => 'bg-yellow-100 text-yellow-700 animate-pulse',
                            'REJECTED' => 'bg-red-100 text-red-700',
                        } }}">
                        {{ $pay->status }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <button wire:click="showDetail('{{ $pay->id }}')" class="bg-slate-900 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-lg shadow-slate-900/10">
                        Check Bukti
                    </button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="p-4">{{ $payments->links() }}</div>
</div>

<!-- MODAL CEK BUKTI -->
@if($isModalOpen && $selectedPayment)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col md:flex-row">
        <!-- Sisi Kiri: Bukti Gambar -->
        <div class="md:w-1/2 bg-slate-100 dark:bg-slate-900 flex items-center justify-center p-4">
            @if($selectedPayment->proof_path)
                <img src="{{ asset('storage/' . $selectedPayment->proof_path) }}" class="max-h-[70vh] rounded-xl shadow-lg border-4 border-white object-contain" alt="Bukti Transfer">
            @else
                <div class="text-slate-400 italic">Gambar tidak ditemukan.</div>
            @endif
        </div>

        <!-- Sisi Kanan: Kontrol Verifikasi -->
        <div class="md:w-1/2 p-8 space-y-6 flex flex-col justify-between">
            <div>
                <div class="flex justify-between items-start mb-6">
                    <h3 class="font-black text-2xl text-slate-900 dark:text-white uppercase">Validasi Dana</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 text-2xl">&times;</button>
                </div>

                <div class="space-y-4">
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-2xl border border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Klaim User</p>
                        <p class="text-xl font-black text-slate-900 dark:text-white">Rp {{ number_format($selectedPayment->amount_paid, 0, ',', '.') }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Metode</p>
                            <p class="text-sm font-bold">{{ $selectedPayment->payment_method }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Tanggal</p>
                            <p class="text-sm font-bold">{{ $selectedPayment->payment_date->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>

                @if($selectedPayment->status == 'PENDING')
                <div class="mt-8">
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Catatan Penolakan (Hanya jika ditolak)</label>
                    <textarea wire:model="rejection_note" class="w-full rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 text-sm" placeholder="Contoh: Gambar bukti transfer buram atau nominal tidak sesuai bank."></textarea>
                    @error('rejection_note') <span class="text-red-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                </div>
                @endif
            </div>

            @if($selectedPayment->status == 'PENDING')
            <div class="flex gap-3 pt-6 border-t border-slate-100 dark:border-slate-700">
                <button wire:click="reject" class="flex-1 py-3 border border-red-200 text-red-600 rounded-xl font-bold hover:bg-red-50 transition-colors">TOLAK</button>
                <button wire:click="approve" class="flex-[2] py-3 bg-green-600 text-white rounded-xl font-black shadow-lg shadow-green-900/20 hover:bg-green-700 transition-all">VERIFIKASI & TERIMA</button>
            </div>
            @else
            <div class="p-4 bg-slate-100 dark:bg-slate-900 text-center rounded-xl text-slate-500 font-bold uppercase text-xs">
                Transaksi ini sudah berstatus: {{ $selectedPayment->status }}
            </div>
            @endif
        </div>
    </div>
</div>
@endif


</div>