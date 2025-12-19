<div class="max-w-5xl mx-auto px-6 font-sans">

    <!-- HEADER -->
    <div class="mb-8 flex flex-col md:flex-row items-start md:items-center gap-4">
        <a href="{{ route('pmb.status') }}"
            class="p-2 bg-white rounded-lg border border-slate-200 text-slate-400 hover:text-brand-blue transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div class="flex-1">
            <h2 class="text-2xl font-black text-slate-900">Daftar Ulang & Pembayaran</h2>
            <p class="text-slate-500 text-sm">Selesaikan administrasi agar Nomor Induk Mahasiswa (NIM) Anda bisa aktif.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- KOLOM KIRI: INFO REKENING & RINGKASAN -->
        <div class="lg:col-span-1 space-y-6">

            <!-- Card Rekening -->
            <div class="bg-brand-blue rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                <h3 class="text-xs font-bold uppercase tracking-widest text-blue-200 mb-4">Rekening Tujuan</h3>

                <div class="space-y-4 relative z-10">
                    <div>
                        <p class="text-[10px] text-blue-300 font-bold uppercase">Nama Bank</p>
                        <p class="text-lg font-black">{{ $settings->bank_name ?? 'BANK BRI' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-300 font-bold uppercase">Nomor Rekening</p>
                        <p class="text-xl font-mono font-black tracking-wider flex items-center justify-between">
                            {{ $settings->bank_account ?? '1234-5678-90' }}
                            <button
                                onclick="navigator.clipboard.writeText('{{ $settings->bank_account }}'); alert('Nomor Rekening disalin!')"
                                class="text-blue-300 hover:text-white">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-300 font-bold uppercase">Atas Nama</p>
                        <p class="text-sm font-bold uppercase">{{ $settings->bank_holder ?? 'YAYASAN STELLA MARIS' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card Ringkasan Tagihan -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-4 border-b pb-2">Ringkasan Biaya</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-500">Total Tagihan</span>
                        <span class="font-bold text-slate-900">Rp
                            {{ number_format($billing->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-green-600">Sudah Dibayar</span>
                        <span class="font-bold text-green-600">Rp
                            {{ number_format($total_verified, 0, ',', '.') }}</span>
                    </div>
                    <div class="pt-2 border-t flex justify-between items-center">
                        <span class="text-xs font-bold uppercase text-red-500">Sisa Tagihan</span>
                        <span class="text-xl font-black text-red-600">Rp
                            {{ number_format($remaining, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if ($remaining > 0)
                    <div
                        class="mt-4 p-3 bg-yellow-50 text-yellow-700 text-[10px] leading-relaxed rounded-xl font-medium border border-yellow-100">
                        *Anda dapat membayar penuh atau mencicil. NIM akan aktif setelah pembayaran pertama
                        diverifikasi.
                    </div>
                @elseif(!$registrant->nim)
                    <div
                        class="mt-4 p-3 bg-green-50 text-green-700 text-center rounded-xl font-bold border border-green-200">
                        🎉 Pembayaran Lunas!<br>
                        NIM sedang diproses oleh admin, mohon tunggu beberapa saat.
                    </div>
                @else
                    <div
                        class="mt-4 p-3 bg-blue-50 text-blue-700 text-center rounded-xl font-bold border border-blue-200">
                        ✅ Pembayaran Lunas & NIM Aktif!<br>
                        <a href="{{ route('/') }}" class="underline font-semibold">Klik untuk masuk ke
                            Siakad & KRS</a>
                    </div>
                @endif
            </div>
        </div>

        <!-- KOLOM KANAN: FORM UNGGAH BUKTI -->
        <div class="lg:col-span-2 space-y-6">
            @if ($remaining > 0)
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl shadow-slate-200/50">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                        </span>
                        Konfirmasi Pembayaran Baru
                    </h3>

                    <form wire:submit.prevent="submitPayment" class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Jumlah yang Dibayar
                                    (Rp)</label>
                                <input wire:model="amount_paid" type="number"
                                    class="w-full rounded-xl border-slate-300 focus:ring-brand-blue font-bold text-lg"
                                    placeholder="Masukkan nominal...">
                                @error('amount_paid')
                                    <span class="text-red-500 text-xs font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Tanggal Transfer</label>
                                <input wire:model="payment_date" type="date"
                                    class="w-full rounded-xl border-slate-300 focus:ring-brand-blue">
                                @error('payment_date')
                                    <span class="text-red-500 text-xs font-bold">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Unggah Bukti Transfer
                                (Asli)</label>
                            <div
                                class="relative border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center hover:bg-slate-50 transition-colors">
                                <input wire:model="proof_file" type="file"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*">
                                @if ($proof_file)
                                    <div class="text-sm text-green-600 font-bold">File Terpilih:
                                        {{ $proof_file->getClientOriginalName() }}</div>
                                @else
                                    <div class="text-slate-400">
                                        <svg class="w-10 h-10 mx-auto mb-2 opacity-50" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Klik atau seret gambar bukti di sini
                                    </div>
                                @endif
                            </div>
                            @error('proof_file')
                                <span class="text-red-500 text-xs font-bold block mt-2">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" wire:loading.attr="disabled"
                                class="px-8 py-3 bg-brand-blue text-white rounded-xl font-bold shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition-all flex items-center gap-2">
                                <span wire:loading.remove>Kirim Bukti Pembayaran</span>
                                <span wire:loading>Sedang Mengirim...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- RIWAYAT TRANSAKSI -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="font-bold text-slate-800">Riwayat Transaksi</h3>
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-100 text-slate-500 uppercase text-[10px] font-black">
                        <tr>
                            <th class="px-6 py-3">Tanggal</th>
                            <th class="px-6 py-3">Jumlah</th>
                            <th class="px-6 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $pay)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-slate-500">{{ $pay->payment_date->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-mono font-bold text-slate-800">Rp
                                    {{ number_format($pay->amount_paid, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($pay->status == 'VERIFIED')
                                        <span class="text-green-600 font-bold uppercase text-[10px]">Diterima</span>
                                    @elseif($pay->status == 'REJECTED')
                                        <span class="text-red-600 font-bold uppercase text-[10px]"
                                            title="{{ $pay->rejection_note }}">Ditolak</span>
                                    @else
                                        <span
                                            class="text-yellow-600 font-bold uppercase text-[10px] animate-pulse">Menunggu</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-slate-400 italic">Belum ada
                                    pembayaran yang diunggah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
