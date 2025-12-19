<div class="px-2 sm:px-4">

    {{-- HEADER --}}
    <x-slot name="header">
        <h2 class="text-lg sm:text-xl font-bold text-brand-blue dark:text-brand-gold">
            Verifikasi Pembayaran
        </h2>
    </x-slot>

    {{-- FLASH MESSAGE --}}
    @if (session()->has('message'))
        <div class="mb-4 p-4 rounded-xl bg-green-100 text-green-800 border border-green-200">
            ✅ {{ session('message') }}
        </div>
    @endif

    {{-- FILTER & SEARCH --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">

        <div class="flex flex-wrap gap-2">
            @foreach (['PENDING'=>'Menunggu','VERIFIED'=>'Diterima','REJECTED'=>'Ditolak'] as $key=>$label)
                <button
                    wire:click="$set('filter_status','{{ $key }}')"
                    class="px-4 py-2 rounded-lg text-xs sm:text-sm font-bold transition
                    {{ $filter_status === $key
                        ? 'bg-brand-blue text-brand-gold shadow'
                        : 'bg-white dark:bg-brand-dark border text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <input
            wire:model.live.debounce.300ms="search"
            type="text"
            placeholder="Cari nama..."
            class="w-full sm:w-64 rounded-lg border-slate-300
                   dark:bg-brand-dark dark:border-slate-700 dark:text-white">
    </div>

    {{-- DESKTOP TABLE --}}
    <div class="hidden md:block bg-white dark:bg-brand-dark rounded-xl shadow border dark:border-slate-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800 text-xs uppercase font-bold text-slate-500">
                <tr>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Mahasiswa / Camaba</th>
                    <th class="px-6 py-4">Tagihan</th>
                    <th class="px-6 py-4">Nominal</th>
                    <th class="px-6 py-4 text-center">Bukti</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-slate-700">
                @forelse ($payments as $pay)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                        <td class="px-6 py-4 text-slate-500">
                            {{ $pay->payment_date->format('d M Y') }}
                            <div class="text-[10px]">{{ $pay->created_at->diffForHumans() }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 dark:text-white">
                                {{ $pay->billing->owner_name }}
                            </div>
                            <div class="text-xs text-slate-500 font-mono">
                                {{ $pay->billing->owner_code }}
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="font-semibold">{{ $pay->billing->title }}</div>
                            <div class="text-xs text-slate-500">
                                Total: Rp {{ number_format($pay->billing->amount,0,',','.') }}
                            </div>
                        </td>

                        <td class="px-6 py-4 font-bold text-green-600 font-mono">
                            Rp {{ number_format($pay->amount_paid,0,',','.') }}
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if ($pay->proof_path)
                                <a href="{{ asset('storage/'.$pay->proof_path) }}" target="_blank"
                                   class="text-brand-blue dark:text-brand-gold text-xs underline">
                                    Lihat
                                </a>
                            @else
                                <span class="text-red-400 text-xs">Tidak ada</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right">
                            <button
                                wire:click="showDetail('{{ $pay->id }}')"
                                class="px-4 py-2 bg-brand-blue text-brand-gold rounded-lg text-xs font-bold">
                                {{ $filter_status === 'PENDING' ? 'Periksa' : 'Detail' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                            Tidak ada data.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $payments->links() }}</div>
    </div>

    {{-- MOBILE CARD --}}
    <div class="md:hidden space-y-4">
        @foreach ($payments as $pay)
            <div class="bg-white dark:bg-brand-dark border dark:border-slate-700 rounded-xl p-4 shadow">
                <div class="flex justify-between mb-2">
                    <div>
                        <p class="font-bold text-brand-blue dark:text-brand-gold">
                            {{ $pay->billing->owner_name }}
                        </p>
                        <p class="text-xs text-slate-500 font-mono">
                            {{ $pay->billing->owner_code }}
                        </p>
                    </div>
                    <span class="text-xs text-slate-400">
                        {{ $pay->payment_date->format('d M Y') }}
                    </span>
                </div>

                <p class="font-semibold text-sm">{{ $pay->billing->title }}</p>
                <p class="text-xs text-slate-500 mb-2">
                    Tagihan: Rp {{ number_format($pay->billing->amount,0,',','.') }}
                </p>

                <div class="flex justify-between items-center">
                    <span class="font-bold text-green-600 font-mono">
                        Rp {{ number_format($pay->amount_paid,0,',','.') }}
                    </span>
                    <button
                        wire:click="showDetail('{{ $pay->id }}')"
                        class="px-4 py-2 bg-brand-blue text-brand-gold rounded-lg text-xs font-bold">
                        {{ $filter_status === 'PENDING' ? 'Periksa' : 'Detail' }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODAL --}}
    @if ($isModalOpen && $selectedPayment)
        <div class="fixed inset-0 bg-black/80 backdrop-blur z-50 flex items-center justify-center p-3">
            <div class="bg-white dark:bg-brand-dark rounded-xl w-full max-w-3xl overflow-hidden shadow-2xl">

                <div class="p-6 border-b dark:border-slate-700">
                    <h3 class="text-xl font-bold text-brand-blue dark:text-brand-gold">
                        Validasi Pembayaran
                    </h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="text-sm">
                        <p class="text-slate-500">Nama</p>
                        <p class="font-bold">
                            {{ $selectedPayment->billing->owner_name }}
                        </p>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-xl">
                        <p class="text-xs font-bold text-green-600 uppercase">Nominal</p>
                        <p class="text-3xl font-black text-green-700">
                            Rp {{ number_format($selectedPayment->amount_paid,0,',','.') }}
                        </p>
                    </div>

                    @if ($selectedPayment->status === 'PENDING')
                        <textarea
                            wire:model="rejection_note"
                            rows="2"
                            class="w-full rounded-lg border-slate-300 dark:bg-slate-800 dark:text-white"
                            placeholder="Catatan penolakan (opsional)">
                        </textarea>
                    @endif
                </div>

                <div class="p-6 flex justify-end gap-3 border-t dark:border-slate-700">
                    <button wire:click="$set('isModalOpen', false)"
                        class="px-4 py-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                        Tutup
                    </button>

                    @if ($selectedPayment->status === 'PENDING')
                        <button wire:click="reject"
                            class="px-4 py-2 rounded-lg border border-red-300 text-red-600 font-bold">
                            Tolak
                        </button>
                        <button wire:click="verify"
                            class="px-6 py-2 bg-brand-blue text-brand-gold rounded-lg font-bold">
                            Verifikasi
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
