<div>
    <x-slot name="header">Kelola Keuangan & Tagihan</x-slot>

    <!-- Alert -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200">
            ✅ {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 font-bold border border-red-200">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <!-- Actions -->
    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <div class="flex gap-2">
            <select wire:model.live="filter_status" class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                <option value="">Semua Status</option>
                <option value="PAID">Lunas (Paid)</option>
                <option value="UNPAID">Belum Lunas (Unpaid)</option>
                <option value="PARTIAL">Dicicil</option>
            </select>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Mahasiswa / Tagihan..." class="rounded-lg border-slate-300 w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
        </div>
        
        <button wire:click="create" class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-500/30">
            + Buat Tagihan Baru
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Mahasiswa</th>
                    <th class="px-6 py-4">Judul Tagihan</th>
                    <th class="px-6 py-4">Nominal</th>
                    <th class="px-6 py-4">Jatuh Tempo</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($billings as $bill)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $bill->student->user->name }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $bill->student->nim }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800 dark:text-slate-200">{{ $bill->title }}</div>
                        <div class="text-xs text-slate-400">{{ Str::limit($bill->description, 30) }}</div>
                    </td>
                    <td class="px-6 py-4 font-mono font-bold text-slate-700 dark:text-slate-300">
                        Rp {{ number_format($bill->amount, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $bill->due_date ? $bill->due_date->format('d M Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($bill->status == 'PAID')
                            <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-1 rounded-full">LUNAS</span>
                        @elseif($bill->status == 'PARTIAL')
                            <span class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-1 rounded-full">CICIL</span>
                        @else
                            <span class="bg-red-100 text-red-800 text-[10px] font-bold px-2 py-1 rounded-full">BELUM</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <!-- TOMBOL DETAIL SUDAH DIAKTIFKAN -->
                        <button wire:click="showDetail('{{ $bill->id }}')" class="text-blue-600 hover:underline text-xs font-bold">Detail</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada data tagihan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $billings->links() }}</div>
    </div>

    <!-- MODAL CREATE -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg my-8">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Buat Tagihan Baru</h3>
            </div>
            
            <form wire:submit.prevent="store" class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-bold mb-2 text-slate-700 dark:text-slate-300">Target Penerima</label>
                    <div class="flex gap-4 mb-3">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="target_type" value="prodi" class="text-brand-blue focus:ring-brand-blue">
                            <span class="ml-2 text-sm">Per Prodi</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="target_type" value="angkatan" class="text-brand-blue focus:ring-brand-blue">
                            <span class="ml-2 text-sm">Per Angkatan</span>
                        </label>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" wire:model.live="target_type" value="individual" class="text-brand-blue focus:ring-brand-blue">
                            <span class="ml-2 text-sm">Mahasiswa Tertentu</span>
                        </label>
                    </div>

                    @if($target_type == 'prodi')
                        <select wire:model="prodi_id" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white">
                            <option value="">-- Pilih Program Studi --</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->degree }})</option>
                            @endforeach
                        </select>
                    @elseif($target_type == 'angkatan')
                        <input wire:model="entry_year" type="number" placeholder="Tahun Angkatan (Misal: 2024)" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white">
                    @elseif($target_type == 'individual')
                        <input wire:model="specific_student_nim" type="text" placeholder="Masukkan NIM Mahasiswa" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white">
                    @endif
                </div>

                <div class="space-y-3 border-t border-slate-100 pt-4 dark:border-slate-700">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Judul Tagihan</label>
                        <input wire:model="title" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white" placeholder="Contoh: SPP Semester Ganjil 2024">
                        @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Nominal (Rp)</label>
                            <input wire:model="amount" type="number" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white" placeholder="0">
                            @error('amount') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Jatuh Tempo</label>
                            <input wire:model="due_date" type="date" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white">
                            @error('due_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex justify-end gap-2 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded bg-brand-blue text-white hover:bg-blue-800 shadow-lg font-bold">
                        Simpan Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL DETAIL (BARU) -->
    @if($isDetailModalOpen && $selectedBillingDetail)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Detail Tagihan</h3>
                <button wire:click="$set('isDetailModalOpen', false)" class="text-slate-400 hover:text-red-500">&times;</button>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Info Utama -->
                <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg border border-slate-100 dark:border-slate-700">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-bold">Mahasiswa</p>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $selectedBillingDetail->student->user->name }}</p>
                        <p class="text-xs text-slate-400">{{ $selectedBillingDetail->student->nim }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500 uppercase font-bold">Total Tagihan</p>
                        <p class="font-mono text-xl font-black text-brand-blue dark:text-brand-gold">
                            Rp {{ number_format($selectedBillingDetail->amount, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-slate-400">{{ $selectedBillingDetail->title }}</p>
                    </div>
                </div>

                <!-- Riwayat Pembayaran -->
                <div>
                    <h4 class="font-bold text-slate-800 dark:text-white mb-3">Riwayat Pembayaran</h4>
                    <div class="overflow-hidden rounded-lg border border-slate-200 dark:border-slate-700">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs uppercase font-bold">
                                <tr>
                                    <th class="px-4 py-2">Tanggal</th>
                                    <th class="px-4 py-2">Jumlah</th>
                                    <th class="px-4 py-2">Bukti</th>
                                    <th class="px-4 py-2 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($selectedBillingDetail->payments as $pay)
                                <tr>
                                    <td class="px-4 py-3">{{ $pay->payment_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 font-mono">Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        @if($pay->proof_path)
                                            <a href="{{ asset('storage/'.$pay->proof_path) }}" target="_blank" class="text-blue-600 hover:underline text-xs">Lihat</a>
                                        @else - @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($pay->status == 'VERIFIED')
                                            <span class="text-green-600 font-bold text-xs">Sah</span>
                                        @elseif($pay->status == 'REJECTED')
                                            <span class="text-red-600 font-bold text-xs" title="{{ $pay->rejection_note }}">Ditolak</span>
                                        @else
                                            <span class="text-yellow-600 font-bold text-xs">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-500 italic">Belum ada pembayaran masuk.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                <button wire:click="$set('isDetailModalOpen', false)" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-white rounded-lg font-bold hover:bg-slate-300">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>