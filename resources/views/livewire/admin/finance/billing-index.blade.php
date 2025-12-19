<div class="space-y-6 font-sans">
    <x-slot name="header">Kelola Keuangan & Tagihan</x-slot>

    <!-- FITUR PINTAR 1: Ringkasan Real-time (Responsive Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-down">
        <!-- Card Total Nominal -->
        <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tagihan (Filter)</p>
            <p class="text-xl font-black text-slate-900 dark:text-white mt-1 break-all">
                Rp {{ number_format($summary['total_bill'], 0, ',', '.') }}
            </p>
        </div>
        
        <!-- Card Paid -->
        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-2xl border border-green-100 dark:border-green-800">
            <p class="text-[10px] font-bold text-green-600 uppercase tracking-wider">Lunas</p>
            <p class="text-xl font-black text-green-700 dark:text-green-400 mt-1">
                {{ $summary['count_paid'] }} <span class="text-xs font-medium text-green-600/70">Orang</span>
            </p>
        </div>

        <!-- Card Partial -->
        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-2xl border border-blue-100 dark:border-blue-800">
            <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Mencicil</p>
            <p class="text-xl font-black text-blue-700 dark:text-blue-400 mt-1">
                {{ $summary['count_partial'] }} <span class="text-xs font-medium text-blue-600/70">Orang</span>
            </p>
        </div>

        <!-- Card Unpaid -->
        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-2xl border border-red-100 dark:border-red-800">
            <p class="text-[10px] font-bold text-red-600 uppercase tracking-wider">Belum Bayar</p>
            <p class="text-xl font-black text-red-700 dark:text-red-400 mt-1">
                {{ $summary['count_unpaid'] }} <span class="text-xs font-medium text-red-600/70">Orang</span>
            </p>
        </div>
    </div>

    <!-- Toolbar Filters (Responsive Stack) -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        
        <div class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto flex-1">
            <!-- Search -->
            <div class="relative flex-1 w-full">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / NIM / No. Reg..."
                    class="w-full pl-10 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all">
                <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <!-- Filter Status -->
            <select wire:model.live="filter_status" class="w-full sm:w-auto rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue cursor-pointer">
                <option value="">Semua Status</option>
                <option value="PAID">✅ Lunas</option>
                <option value="UNPAID">❌ Belum Lunas</option>
                <option value="PARTIAL">⏳ Dicicil</option>
            </select>

            <!-- Filter Jenis Biaya -->
            <select wire:model.live="filter_fee_type" class="w-full sm:w-auto rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue cursor-pointer">
                <option value="">Semua Jenis Biaya</option>
                @foreach($fee_types as $ft)
                    <option value="{{ $ft->id }}">{{ $ft->name }}</option>
                @endforeach
            </select>
        </div>

        <button wire:click="create" class="w-full sm:w-auto flex justify-center items-center gap-2 px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 transform hover:-translate-y-0.5 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Buat Tagihan Masal
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Pembayar</th>
                        <th class="px-6 py-4">Detail Tagihan</th>
                        <th class="px-6 py-4 text-right">Nominal & Sisa</th>
                        <th class="px-6 py-4 text-center">Status Pembayaran</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($billings as $bill)
                        @php
                            // Logic Pintar Display Nama (Mahasiswa / Camaba)
                            if ($bill->student) {
                                $name = $bill->student->user->name ?? 'Unknown';
                                $identity = $bill->student->nim;
                                $typeLabel = 'Mahasiswa';
                                $avatarColor = 'bg-blue-50 text-blue-600';
                            } elseif ($bill->registrant) {
                                $name = $bill->registrant->user->name ?? 'Unknown';
                                $identity = $bill->registrant->registration_no;
                                $typeLabel = 'Camaba';
                                $avatarColor = 'bg-purple-50 text-purple-600';
                            } else {
                                $name = 'Hantu (Data Hilang)';
                                $identity = '-';
                                $typeLabel = 'Unknown';
                                $avatarColor = 'bg-slate-100 text-slate-500';
                            }

                            $isOverdue = $bill->status != 'PAID' && $bill->due_date < now();
                            $paidAmount = $bill->payments->where('status', 'VERIFIED')->sum('amount_paid');
                            $percentage = $bill->amount > 0 ? ($paidAmount / $bill->amount) * 100 : 0;
                            $remaining = max(0, $bill->amount - $paidAmount);
                        @endphp
                        
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full {{ $avatarColor }} flex items-center justify-center font-bold text-xs shadow-sm">
                                        {{ substr($name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">{{ $name }}</div>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[9px] font-bold uppercase px-1.5 py-0.5 rounded border {{ $bill->student ? 'bg-blue-50 border-blue-100 text-blue-600' : 'bg-purple-50 border-purple-100 text-purple-600' }}">
                                                {{ $typeLabel }}
                                            </span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $identity }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $bill->fee_type?->name ?? 'Lainnya' }}
                                        </span>
                                        @if($bill->semester)
                                            <span class="text-[9px] font-bold text-slate-400 bg-slate-50 px-1.5 rounded border border-slate-100">SMT {{ $bill->semester }}</span>
                                        @endif
                                    </div>
                                    <span class="font-medium text-slate-800 dark:text-slate-200 text-xs truncate max-w-[150px] sm:max-w-xs" title="{{ $bill->title }}">{{ $bill->title }}</span>
                                    
                                    @if($isOverdue)
                                        <span class="flex items-center gap-1 text-[10px] font-bold text-red-500 animate-pulse">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Jatuh Tempo: {{ $bill->due_date->format('d M Y') }}
                                        </span>
                                    @else
                                        <span class="text-[10px] text-slate-400">Jatuh Tempo: {{ $bill->due_date ? $bill->due_date->format('d M') : '-' }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="font-mono font-black text-slate-700 dark:text-slate-300">
                                    Rp {{ number_format($bill->amount, 0, ',', '.') }}
                                </div>
                                @if($bill->status == 'PARTIAL')
                                    <div class="text-[10px] font-bold text-red-500">
                                        Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-middle">
                                <div class="flex flex-col items-center gap-1.5">
                                    @if ($bill->status == 'PAID')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 border border-green-200">
                                            ✅ LUNAS
                                        </span>
                                    @elseif($bill->status == 'PARTIAL')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                            ⏳ DICICIL ({{ round($percentage) }}%)
                                        </span>
                                        <div class="w-20 h-1.5 bg-slate-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500" style="width: {{ $percentage }}%"></div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                            ❌ BELUM BAYAR
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="showDetail('{{ $bill->id }}')" class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 bg-slate-100 hover:bg-brand-blue hover:text-white transition-all">
                                    Rincian
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <p class="text-sm font-medium">Belum ada data tagihan ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700">
            {{ $billings->links() }}
        </div>
    </div>

    <!-- MODAL CREATE (RESPONSIVE) -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
            <!-- Modal Container: Full width on mobile (bottom sheet feel), max-w-lg on desktop -->
            <div class="bg-white dark:bg-slate-800 rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl w-full max-w-lg mx-auto transform transition-all scale-100 overflow-hidden flex flex-col max-h-[90vh] sm:max-h-auto">
                
                <!-- Header -->
                <div class="px-6 sm:px-8 py-5 sm:py-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/50 flex-shrink-0">
                    <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">Buat Tagihan Baru</h3>
                    <p class="text-xs text-slate-500 mt-1">Generate tagihan masal untuk mahasiswa aktif.</p>
                </div>

                <!-- Scrollable Content -->
                <div class="overflow-y-auto p-6 sm:p-8 space-y-6">
                    @if (session()->has('message') || session()->has('error'))
                        <div class="space-y-2">
                            @if (session()->has('message'))
                                <div class="p-4 bg-green-100 text-green-700 rounded-xl font-bold border border-green-200 flex items-center gap-2 text-xs sm:text-sm">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    {{ session('message') }}
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="p-4 bg-red-100 text-red-700 rounded-xl font-bold border border-red-200 flex items-center gap-2 text-xs sm:text-sm">
                                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ session('error') }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <form wire:submit.prevent="store" id="createForm" class="space-y-6">
                        
                        <!-- 1. Target -->
                        <div>
                            <label class="block text-sm font-bold mb-3 text-slate-700 dark:text-slate-300">Target Mahasiswa</label>
                            
                            <!-- Filter Status Mahasiswa -->
                            <div class="mb-4 bg-white border border-slate-200 p-3 rounded-xl">
                                <label class="block text-[10px] font-bold mb-2 text-slate-400 uppercase">Status Akademik</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['A' => 'Aktif', 'C' => 'Cuti', 'N' => 'Non-Aktif', 'All' => 'Semua'] as $val => $label)
                                        <label class="cursor-pointer flex-1 sm:flex-none">
                                            <input type="radio" wire:model.live="target_student_status" value="{{ $val }}" class="peer sr-only">
                                            <div class="text-center px-3 py-1.5 rounded-lg text-[10px] font-bold border border-slate-200 text-slate-500 peer-checked:bg-slate-800 peer-checked:text-white peer-checked:border-slate-800 transition-all hover:bg-slate-50">
                                                {{ $label }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('target_student_status') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                                
                                <!-- PENGAMAN PINTAR: Warning untuk 'All' -->
                                @if($target_student_status == 'All')
                                    <div class="mt-2 p-2 bg-orange-50 border border-orange-100 rounded-lg flex gap-2 items-start animate-fade-in">
                                        <svg class="w-4 h-4 text-orange-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        <p class="text-[10px] text-orange-600 leading-tight">
                                            <strong>Hati-hati!</strong> Memilih "Semua" akan menagih mahasiswa yang sudah Lulus/Keluar jika datanya masih ada.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                                @foreach(['prodi' => 'Per Prodi', 'angkatan' => 'Per Angkatan', 'individual' => 'Individu'] as $key => $label)
                                    <label class="cursor-pointer group">
                                        <input type="radio" wire:model.live="target_type" value="{{ $key }}" class="peer sr-only">
                                        <div class="p-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 text-center peer-checked:border-brand-blue peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all hover:bg-slate-50">
                                            <span class="block text-xs font-bold text-slate-500 peer-checked:text-brand-blue">{{ $label }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @error('target_type') <span class="text-red-500 text-xs font-bold mt-1 mb-2 block">{{ $message }}</span> @enderror

                            <div class="bg-slate-50 dark:bg-slate-700/50 p-4 rounded-xl border border-slate-100 dark:border-slate-700 transition-all">
                                @if ($target_type == 'prodi')
                                    <div class="space-y-3">
                                        <select wire:model="prodi_id" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                            <option value="">-- Pilih Program Studi --</option>
                                            @foreach ($prodis as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->degree }})</option>
                                            @endforeach
                                        </select>
                                        @error('prodi_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                        
                                        <input wire:model="entry_year" type="number" placeholder="Khusus Angkatan Tahun (Opsional)" 
                                            class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue" title="Kosongkan jika ingin semua angkatan">
                                        @error('entry_year') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                        <p class="text-[10px] text-slate-400 ml-1">*Isi tahun jika ingin menagih angkatan tertentu saja di Prodi ini.</p>
                                    </div>
                                @elseif($target_type == 'angkatan')
                                    <input wire:model="entry_year" type="number" placeholder="Tahun Angkatan (Misal: 2024)" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                    @error('entry_year') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                @elseif($target_type == 'individual')
                                    <input wire:model="specific_student_nim" type="text" placeholder="Masukkan NIM Mahasiswa" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                    @error('specific_student_nim') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>
                        </div>

                        <!-- 2. Detail Tagihan -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold mb-1 text-slate-700">Jenis Biaya</label>
                                    <select wire:model.live="fee_type_id" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                        @foreach ($fee_types as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('fee_type_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold mb-1 text-slate-700">Semester</label>
                                    <input wire:model="semester" type="number" min="1" max="14" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue" placeholder="Opsional">
                                    @error('semester') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1 text-slate-700">Judul Tagihan</label>
                                <input wire:model="title" type="text" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue" placeholder="Contoh: SPP Semester Ganjil 2024">
                                @error('title') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Smart Amount Toggle & Formatter -->
                            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-sm font-bold text-slate-700">Nominal Tagihan</label>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] uppercase font-bold {{ !$use_manual_amount ? 'text-brand-blue' : 'text-slate-400' }}">Otomatis</span>
                                        <button type="button" wire:click="$toggle('use_manual_amount')" 
                                            class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors focus:outline-none {{ $use_manual_amount ? 'bg-brand-blue' : 'bg-slate-300' }}">
                                            <span class="inline-block h-3 w-3 transform rounded-full bg-white transition {{ $use_manual_amount ? 'translate-x-5' : 'translate-x-1' }}"></span>
                                        </button>
                                        <span class="text-[10px] uppercase font-bold {{ $use_manual_amount ? 'text-brand-blue' : 'text-slate-400' }}">Manual</span>
                                    </div>
                                </div>

                                @if($use_manual_amount)
                                    <!-- ALPINE JS CURRENCY FORMATTER -->
                                    <div class="relative animate-fade-in"
                                         x-data="{
                                            displayValue: '',
                                            init() {
                                                this.$watch('$wire.amount', (value) => {
                                                    if (value === null || value === undefined || value === '') {
                                                        this.displayValue = '';
                                                        return;
                                                    }
                                                    this.displayValue = this.formatRupiah(value.toString());
                                                });
                                                if ($wire.amount) {
                                                    this.displayValue = this.formatRupiah($wire.amount.toString());
                                                }
                                            },
                                            formatRupiah(angka) {
                                                if (!angka) return '';
                                                let number_string = angka.replace(/[^0-9]/g, '').toString();
                                                let sisa = number_string.length % 3;
                                                let rupiah = number_string.substr(0, sisa);
                                                let ribuan = number_string.substr(sisa).match(/\d{3}/g);
                                                if (ribuan) {
                                                    let separator = sisa ? '.' : '';
                                                    rupiah += separator + ribuan.join('.');
                                                }
                                                return rupiah;
                                            },
                                            handleInput(e) {
                                                let raw = e.target.value.replace(/\./g, '');
                                                this.displayValue = this.formatRupiah(raw);
                                                $wire.set('amount', raw);
                                            }
                                         }"
                                         x-init="init()"
                                    >
                                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold">Rp</span>
                                        <input 
                                            type="text" 
                                            x-model="displayValue"
                                            @input="handleInput"
                                            class="w-full pl-10 rounded-xl border-slate-300 text-sm font-mono font-bold focus:ring-brand-blue" 
                                            placeholder="0"
                                        >
                                    </div>
                                    @error('amount') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                @else
                                    <div class="text-xs text-slate-500 italic bg-blue-50 p-2 rounded border border-blue-100 flex gap-2 items-center animate-fade-in">
                                        <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        <span>Sistem mengambil nominal dari Master Tarif.</span>
                                    </div>
                                    @error('amount') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                @endif
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1 text-slate-700">Jatuh Tempo</label>
                                <input wire:model="due_date" type="date" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                @error('due_date') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="flex items-start gap-2">
                                <input type="checkbox" wire:model="skip_duplicates" id="skip_dup" class="mt-0.5 rounded text-brand-blue focus:ring-brand-blue">
                                <label for="skip_dup" class="text-xs text-slate-600 leading-tight">Lewati mahasiswa yang sudah memiliki tagihan yang sama (Cegah Duplikat)</label>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="px-6 sm:px-8 py-5 sm:py-6 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-6 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 font-bold transition text-sm">Batal</button>
                    <!-- SAFETY GUARD: Confirmation Dialog -->
                    <button type="submit" form="createForm" 
                        wire:confirm="Apakah Anda yakin ingin membuat tagihan massal ini? Pastikan filter target sudah benar karena proses ini tidak dapat dibatalkan."
                        wire:loading.attr="disabled" wire:target="store" 
                        class="px-8 py-2.5 rounded-xl bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-lg transition flex items-center gap-2 disabled:opacity-50 text-sm">
                        <span wire:loading.remove wire:target="store">Proses Tagihan</span>
                        <span wire:loading wire:target="store">Sedang Memproses...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL DETAIL (RESPONSIVE) -->
    @if ($isDetailModalOpen && $selectedBillingDetail)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl w-full max-w-2xl overflow-hidden animate-fade-in-up flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 flex-shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Rincian Pembayaran</h3>
                        <p class="text-xs text-slate-500">ID Tagihan: #{{ $selectedBillingDetail->id }}</p>
                    </div>
                    <button wire:click="$set('isDetailModalOpen', false)" class="p-2 hover:bg-slate-200 rounded-full transition">&times;</button>
                </div>

                <div class="p-6 space-y-6 overflow-y-auto">
                    <!-- Info Header Responsive -->
                    <div class="flex flex-col sm:flex-row justify-between items-start bg-slate-50 p-4 rounded-xl border border-slate-100 gap-4">
                        @php
                            // Logic Pintar juga di Detail
                            if ($selectedBillingDetail->student) {
                                $detailName = $selectedBillingDetail->student->user->name ?? '-';
                                $detailId = $selectedBillingDetail->student->nim;
                                $detailLabel = 'MAHASISWA';
                            } elseif ($selectedBillingDetail->registrant) {
                                $detailName = $selectedBillingDetail->registrant->user->name ?? '-';
                                $detailId = $selectedBillingDetail->registrant->registration_no;
                                $detailLabel = 'CAMABA';
                            } else {
                                $detailName = '-';
                                $detailId = '-';
                                $detailLabel = 'UNKNOWN';
                            }
                        @endphp
                        <div class="w-full sm:w-auto">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-[10px] uppercase font-bold text-slate-400">Pembayar</p>
                                <span class="text-[9px] font-black bg-white border border-slate-200 px-1.5 rounded text-slate-500">{{ $detailLabel }}</span>
                            </div>
                            <p class="font-bold text-slate-800 text-lg">{{ $detailName }}</p>
                            <p class="text-xs font-mono text-slate-500">{{ $detailId }}</p>
                        </div>
                        <div class="w-full sm:w-auto text-left sm:text-right">
                             <p class="text-[10px] uppercase font-bold text-slate-400">Tagihan</p>
                             <p class="font-bold text-slate-800">{{ $selectedBillingDetail->title }}</p>
                             <span class="inline-block mt-1 bg-brand-blue text-white text-[10px] font-bold px-2 py-0.5 rounded">{{ $selectedBillingDetail->fee_type?->name ?? 'Lainnya' }}</span>
                        </div>
                    </div>

                    <!-- Summary Angka Responsive Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase">Total Tagihan</p>
                            <p class="font-mono font-black text-slate-800">Rp {{ number_format($selectedBillingDetail->amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-xl border border-green-100">
                            <p class="text-[10px] font-bold text-green-600 uppercase">Sudah Dibayar</p>
                            <p class="font-mono font-black text-green-600">Rp {{ number_format($total_paid, 0, ',', '.') }}</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-xl border border-red-100">
                            <p class="text-[10px] font-bold text-red-500 uppercase">Sisa Tagihan</p>
                            <p class="font-mono font-black text-red-500">Rp {{ number_format($remaining_balance, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- History Table -->
                    <div>
                        <h4 class="text-xs font-bold uppercase text-slate-500 mb-2">Riwayat Pembayaran</h4>
                        <div class="border border-slate-100 rounded-xl overflow-hidden overflow-x-auto">
                            <table class="w-full text-xs text-left whitespace-nowrap">
                                <thead class="bg-slate-50 font-bold text-slate-500">
                                    <tr>
                                        <th class="px-4 py-2">Tanggal</th>
                                        <th class="px-4 py-2">Metode</th>
                                        <th class="px-4 py-2">Nominal</th>
                                        <th class="px-4 py-2 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($selectedBillingDetail->payments as $pay)
                                        <tr>
                                            <td class="px-4 py-2">{{ $pay->payment_date->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2">{{ $pay->payment_method }}</td>
                                            <td class="px-4 py-2 font-bold">Rp {{ number_format($pay->amount_paid, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2 text-center">
                                                @if($pay->status == 'VERIFIED') <span class="text-green-600 font-bold">LUNAS</span>
                                                @elseif($pay->status == 'PENDING') <span class="text-orange-500 font-bold">MENUNGGU</span>
                                                @else <span class="text-red-500 font-bold">DITOLAK</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="px-4 py-4 text-center text-slate-400 italic">Belum ada pembayaran masuk.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>