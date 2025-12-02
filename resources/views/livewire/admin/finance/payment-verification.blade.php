<div>
    <x-slot name="header">Verifikasi Pembayaran</x-slot>

    <!-- Alert -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200">
            ✅ {{ session('message') }}
        </div>
    @endif

    <!-- Filter -->
    <div class="flex justify-between mb-6 gap-4">
        <div class="flex gap-2">
            <button wire:click="$set('filter_status', 'PENDING')" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $filter_status == 'PENDING' ? 'bg-yellow-500 text-white shadow-lg shadow-yellow-500/30' : 'bg-white text-slate-500 hover:bg-slate-100 border border-slate-200' }}">
                Menunggu ({{ \App\Models\Payment::where('status', 'PENDING')->count() }})
            </button>
            <button wire:click="$set('filter_status', 'VERIFIED')" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $filter_status == 'VERIFIED' ? 'bg-green-600 text-white shadow-lg shadow-green-600/30' : 'bg-white text-slate-500 hover:bg-slate-100 border border-slate-200' }}">
                Riwayat Diterima
            </button>
            <button wire:click="$set('filter_status', 'REJECTED')" 
                    class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $filter_status == 'REJECTED' ? 'bg-red-600 text-white shadow-lg shadow-red-600/30' : 'bg-white text-slate-500 hover:bg-slate-100 border border-slate-200' }}">
                Ditolak
            </button>
        </div>
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama Mahasiswa..." class="rounded-lg border-slate-300 w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Tanggal Bayar</th>
                    <th class="px-6 py-4">Mahasiswa</th>
                    <th class="px-6 py-4">Tagihan</th>
                    <th class="px-6 py-4">Nominal Masuk</th>
                    <th class="px-6 py-4 text-center">Bukti</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($payments as $pay)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <td class="px-6 py-4 text-slate-500">
                        {{ $pay->payment_date->format('d M Y') }}
                        <div class="text-[10px]">{{ $pay->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $pay->billing->student->user->name }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $pay->billing->student->nim }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $pay->billing->title }}</div>
                        <div class="text-[10px] text-slate-400">Total Tagihan: Rp {{ number_format($pay->billing->amount, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 font-bold text-green-600 font-mono text-base">
                        Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($pay->proof_path)
                            <a href="{{ asset('storage/'.$pay->proof_path) }}" target="_blank" class="text-blue-500 hover:underline text-xs">Lihat</a>
                        @else
                            <span class="text-red-400 text-xs">Tidak ada</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="showDetail('{{ $pay->id }}')" class="px-4 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-brand-blue transition shadow-md">
                            {{ $filter_status == 'PENDING' ? 'Periksa' : 'Detail' }}
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        Tidak ada data pembayaran pada status ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $payments->links() }}</div>
    </div>

    <!-- MODAL VERIFIKASI -->
    @if($isModalOpen && $selectedPayment)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/90 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col md:flex-row max-h-[90vh]">
            
            <!-- Kolom Kiri: Bukti Gambar -->
            <div class="w-full md:w-1/2 bg-slate-200 dark:bg-slate-900 flex items-center justify-center p-4 overflow-hidden">
                @if($selectedPayment->proof_path)
                    <img src="{{ asset('storage/'.$selectedPayment->proof_path) }}" class="max-h-full max-w-full object-contain rounded shadow-lg">
                @else
                    <div class="text-slate-400">Tidak ada file bukti.</div>
                @endif
            </div>

            <!-- Kolom Kanan: Form Aksi -->
            <div class="w-full md:w-1/2 p-6 flex flex-col">
                <div class="mb-4 border-b border-slate-100 dark:border-slate-700 pb-4">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Validasi Pembayaran</h3>
                    <p class="text-sm text-slate-500">Mohon periksa kesesuaian nominal dan tanggal.</p>
                </div>

                <div class="space-y-4 flex-1 overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-slate-400 text-xs uppercase font-bold">Mahasiswa</p>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $selectedPayment->billing->student->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase font-bold">NIM</p>
                            <p class="font-mono text-slate-800 dark:text-white">{{ $selectedPayment->billing->student->nim }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase font-bold">Tanggal Transfer</p>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $selectedPayment->payment_date->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 text-xs uppercase font-bold">Metode</p>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $selectedPayment->payment_method }}</p>
                        </div>
                    </div>

                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800">
                        <p class="text-xs text-green-600 font-bold uppercase">Nominal Masuk</p>
                        <p class="text-3xl font-black text-green-700 dark:text-green-400">Rp {{ number_format($selectedPayment->amount_paid, 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-500 mt-1">Untuk tagihan: {{ $selectedPayment->billing->title }}</p>
                    </div>

                    @if($selectedPayment->status == 'PENDING')
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan (Jika Ditolak)</label>
                            <textarea wire:model="rejection_note" rows="2" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white" placeholder="Contoh: Bukti buram, nominal kurang..."></textarea>
                        </div>
                    @elseif($selectedPayment->status == 'REJECTED')
                         <div class="p-4 bg-red-50 text-red-700 text-sm rounded-lg border border-red-100">
                            <strong>Alasan Penolakan:</strong><br>
                            {{ $selectedPayment->rejection_note }}
                         </div>
                    @endif
                </div>

                <div class="pt-6 mt-auto flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                    <button wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:text-slate-400 dark:hover:bg-slate-700">Tutup</button>
                    
                    @if($selectedPayment->status == 'PENDING')
                        <button wire:click="reject" wire:confirm="Yakin tolak pembayaran ini?" class="px-4 py-2 rounded-lg border border-red-200 text-red-600 font-bold hover:bg-red-50">
                            Tolak
                        </button>
                        <button wire:click="verify" wire:confirm="Verifikasi pembayaran ini valid?" class="px-6 py-2 rounded-lg bg-green-600 text-white font-bold hover:bg-green-700 shadow-lg shadow-green-500/30">
                            TERIMA (VERIFIKASI)
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>