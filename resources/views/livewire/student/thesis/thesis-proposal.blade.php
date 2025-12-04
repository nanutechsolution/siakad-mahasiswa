<div class="mx-auto max-w-4xl space-y-6 font-sans">
    <x-slot name="header">Tugas Akhir & Skripsi</x-slot>

    <!-- 1. HEADER & STATUS -->
    <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 p-8 text-white shadow-xl">
        <!-- Background FX -->
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 rounded-full bg-brand-blue/50 blur-[80px]"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-64 h-64 rounded-full bg-brand-gold/20 blur-[80px]"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start gap-6">
            <div>
                <h2 class="text-2xl font-black tracking-tight">Pengajuan Judul Skripsi</h2>
                <p class="text-slate-400 mt-1 text-sm">Langkah awal menuju kelulusan. Pastikan judul dan abstrak sudah dikonsultasikan.</p>
            </div>
            
            <!-- Status Badge Dinamis -->
            @if($thesis)
                @php
                    $statusColor = match($thesis->status) {
                        'APPROVED' => 'bg-green-500 text-white ring-green-400/50',
                        'REJECTED' => 'bg-red-500 text-white ring-red-400/50',
                        'COMPLETED' => 'bg-blue-500 text-white ring-blue-400/50',
                        default => 'bg-yellow-500 text-white ring-yellow-400/50' // Proposed
                    };
                    $statusLabel = match($thesis->status) {
                        'APPROVED' => 'DISETUJUI',
                        'REJECTED' => 'DITOLAK / REVISI',
                        'COMPLETED' => 'SELESAI',
                        'ON_PROGRESS' => 'BIMBINGAN',
                        default => 'MENUNGGU VALIDASI'
                    };
                @endphp
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg ring-2 {{ $statusColor }}">
                    @if($thesis->status == 'PROPOSED') <span class="animate-pulse">●</span> @endif
                    {{ $statusLabel }}
                </div>
            @else
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700 text-slate-300 text-xs font-black uppercase tracking-widest shadow-lg">
                    BELUM MENGAJUKAN
                </div>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-2xl bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-3 shadow-sm">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-2xl bg-red-100 text-red-700 font-bold border border-red-200 flex items-center gap-3 shadow-sm">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- 2. FORM PENGAJUAN -->
    <!-- Form dikunci (disabled) jika status APPROVED/COMPLETED agar tidak diubah sembarangan -->
    @php
        $isLocked = $thesis && in_array($thesis->status, ['APPROVED', 'ON_PROGRESS', 'COMPLETED']);
    @endphp

    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-lg border border-slate-100 dark:border-slate-700 p-8 relative overflow-hidden">
        @if($isLocked)
            <div class="absolute top-0 right-0 p-6 opacity-10 pointer-events-none">
                <svg class="w-40 h-40 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
            </div>
        @endif

        <form wire:submit.prevent="save" class="space-y-6 relative z-10">
            
            <!-- Judul Skripsi -->
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">Judul Skripsi / Tugas Akhir</label>
                <textarea wire:model="title" rows="3" 
                    class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:border-brand-blue focus:ring-brand-blue font-bold text-lg disabled:opacity-60 disabled:bg-slate-100 dark:disabled:bg-slate-800"
                    placeholder="Contoh: Rancang Bangun Sistem Informasi Akademik..."
                    {{ $isLocked ? 'disabled' : '' }}></textarea>
                @error('title') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <!-- Abstrak -->
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">Abstrak Ringkas</label>
                <textarea wire:model="abstract" rows="6" 
                    class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:border-brand-blue focus:ring-brand-blue text-sm leading-relaxed text-slate-600 disabled:opacity-60 disabled:bg-slate-100 dark:disabled:bg-slate-800"
                    placeholder="Jelaskan latar belakang, tujuan, dan metode secara singkat..."
                    {{ $isLocked ? 'disabled' : '' }}></textarea>
                @error('abstract') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <!-- Upload Proposal -->
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 uppercase tracking-wide">File Proposal (PDF)</label>
                
                @if($existing_file)
                    <div class="flex items-center gap-3 mb-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                        <div class="p-2 bg-white dark:bg-slate-800 rounded-lg shadow-sm">
                            <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-700 dark:text-slate-300 truncate">File Proposal Tersimpan</p>
                            <a href="{{ asset('storage/'.$existing_file) }}" target="_blank" class="text-xs text-blue-600 hover:underline font-medium">Lihat / Download</a>
                        </div>
                    </div>
                @endif

                @if(!$isLocked)
                    <input wire:model="file" type="file" accept="application/pdf" 
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-brand-blue hover:file:text-white transition-all cursor-pointer">
                    <p class="text-[10px] text-slate-400 mt-2">*Maksimal 5MB. Format PDF.</p>
                    @error('file') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                    
                    <!-- Loading Indicator -->
                    <div wire:loading wire:target="file" class="mt-2 text-xs text-brand-blue font-bold flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Sedang mengupload...
                    </div>
                @endif
            </div>

            @if(!$isLocked)
                <div class="pt-6 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                    <button type="submit" class="px-8 py-3.5 bg-brand-blue text-white rounded-xl font-bold shadow-xl shadow-blue-900/20 hover:bg-blue-800 hover:scale-[1.02] active:scale-95 transition-all transform flex items-center gap-2">
                        <span>{{ $thesis ? 'Update Pengajuan' : 'Ajukan Judul' }}</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                    </button>
                </div>
            @endif

        </form>
    </div>
</div>