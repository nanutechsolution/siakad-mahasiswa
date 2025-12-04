<div class="mx-auto max-w-4xl space-y-6 font-sans">
    <x-slot name="header">Kartu Bimbingan</x-slot>

    <!-- 1. HEADER SKRIPSI -->
    <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 p-8 text-white shadow-xl">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 rounded-full bg-brand-blue/50 blur-[80px]"></div>
        
        <div class="relative z-10">
            <h2 class="text-xl md:text-2xl font-black tracking-tight mb-2 leading-tight">
                {{ $thesis->title }}
            </h2>
            
            <div class="mt-4 flex flex-col md:flex-row gap-4 md:gap-8 text-sm text-slate-300">
                @foreach($thesis->supervisors as $spv)
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-white/10 flex items-center justify-center font-bold text-brand-gold">
                        {{ $spv->role }}
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase text-slate-500">Pembimbing {{ $spv->role }}</p>
                        <p class="font-bold text-white">{{ $spv->lecturer->user->name }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6 pt-6 border-t border-white/10 flex justify-between items-center">
                <p class="text-xs font-mono text-slate-400">Total Bimbingan: <span class="text-white font-bold">{{ $logs->count() }}x</span></p>
                <button wire:click="$set('isModalOpen', true)" class="px-5 py-2 bg-brand-blue hover:bg-blue-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/30 transition-transform hover:-translate-y-1">
                    + Catat Bimbingan Baru
                </button>
            </div>
        </div>
    </div>

    <!-- 2. TIMELINE BIMBINGAN -->
    <div class="relative pl-8 border-l-2 border-slate-200 dark:border-slate-700 space-y-8 ml-4">
        
        @forelse($logs as $log)
        <div class="relative group">
            <!-- Dot Status -->
            <div class="absolute -left-[39px] top-6 h-5 w-5 rounded-full border-4 border-white dark:border-slate-900 shadow-sm {{ $log->status == 'APPROVED' ? 'bg-green-500' : 'bg-yellow-400 animate-pulse' }}"></div>
            
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 hover:border-brand-blue/30 transition-all">
                
                <!-- Header Log -->
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Tanggal Bimbingan</p>
                        <p class="font-black text-lg text-slate-900 dark:text-white">{{ $log->guidance_date->format('d F Y') }}</p>
                    </div>
                    
                    @if($log->status == 'APPROVED')
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-bold border border-green-100">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                VALID
                            </span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-yellow-50 text-yellow-700 text-xs font-bold border border-yellow-100">
                                Menunggu Validasi
                            </span>
                            <!-- Hapus hanya jika belum di-ACC -->
                            <button wire:click="delete('{{ $log->id }}')" wire:confirm="Hapus catatan ini?" class="text-red-400 hover:text-red-600 p-1">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Isi Bimbingan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kiri: Laporan Mahasiswa -->
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Laporan Anda</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line bg-slate-50 dark:bg-slate-700/50 p-3 rounded-xl">
                            {{ $log->student_notes }}
                        </p>
                        @if($log->file_attachment)
                            <a href="{{ asset('storage/'.$log->file_attachment) }}" target="_blank" class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-brand-blue hover:underline">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                File Draft.pdf
                            </a>
                        @endif
                    </div>

                    <!-- Kanan: Feedback Dosen -->
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Catatan Pembimbing</p>
                        @if($log->notes)
                            <div class="p-3 rounded-xl bg-yellow-50 border border-yellow-100 dark:bg-yellow-900/20 dark:border-yellow-800">
                                <p class="text-sm text-slate-800 dark:text-yellow-100 whitespace-pre-line">{{ $log->notes }}</p>
                            </div>
                        @else
                            <p class="text-sm text-slate-400 italic border border-dashed border-slate-200 rounded-xl p-3">Belum ada catatan dari dosen.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-700 ml-[-20px]">
            <p class="text-slate-500">Belum ada riwayat bimbingan. Mulai catat progres skripsimu!</p>
        </div>
        @endforelse
    </div>

    <!-- MODAL FORM TAMBAH -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="w-full max-w-lg bg-white dark:bg-slate-800 rounded-2xl shadow-2xl my-8">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Catat Bimbingan Baru</h3>
            </div>
            
            <form wire:submit.prevent="store" class="p-6 space-y-5">
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Bimbingan</label>
                    <input wire:model="guidance_date" type="date" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                    @error('guidance_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Materi / Topik Bimbingan</label>
                    <textarea wire:model="progress_report" rows="4" class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white" placeholder="Contoh: Revisi Bab 1 Latar Belakang dan Penambahan Referensi..."></textarea>
                    @error('progress_report') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Upload Draft (Opsional)</label>
                    <input wire:model="file" type="file" accept=".pdf,.doc,.docx" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-blue file:text-white hover:file:bg-blue-800">
                    <p class="text-[10px] text-slate-400 mt-1">Format: PDF/DOC. Max: 5MB.</p>
                    @error('file') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 font-bold">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-bold shadow-lg">
                        <span wire:loading.remove wire:target="file">Simpan Catatan</span>
                        <span wire:loading wire:target="file">Uploading...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>