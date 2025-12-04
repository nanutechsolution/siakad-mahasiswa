<div class="mx-auto max-w-5xl space-y-6">
    <x-slot name="header">Kartu Bimbingan Digital</x-slot>

    <!-- Header Skripsi -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $thesis->title }}</h1>
                <p class="text-slate-500 mt-1">Mahasiswa: <span class="font-bold text-brand-blue">{{ $thesis->student->user->name }}</span> ({{ $thesis->student->nim }})</p>
            </div>
            <a href="{{ route('lecturer.thesis.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600">Kembali</a>
        </div>
    </div>

    <!-- Timeline Log -->
    <div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-700 space-y-8">
        @forelse($logs as $log)
        <div class="relative group">
            <!-- Dot Indikator -->
            <div class="absolute -left-[31px] top-6 h-4 w-4 rounded-full border-4 border-white dark:border-slate-900 {{ $log->status == 'APPROVED' ? 'bg-green-500' : 'bg-yellow-400 animate-pulse' }} shadow-sm"></div>
            
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase mb-1">Tanggal Bimbingan</p>
                        <p class="font-bold text-slate-800 dark:text-white">{{ $log->guidance_date->format('d F Y') }}</p>
                    </div>
                    @if($log->status == 'APPROVED')
                        <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Divalidasi</span>
                    @else
                        <button wire:click="validateLog('{{ $log->id }}')" class="bg-brand-blue text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-800 shadow-lg shadow-blue-500/30 transition">
                            Validasi / Beri Catatan
                        </button>
                    @endif
                </div>

                <!-- Konten Bimbingan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-50 dark:bg-slate-700/30 p-4 rounded-xl">
                        <p class="text-xs font-bold text-slate-500 mb-2 uppercase">Laporan Mahasiswa</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $log->student_notes }}</p>
                        
                        @if($log->file_attachment)
                            <a href="{{ asset('storage/'.$log->file_attachment) }}" target="_blank" class="mt-3 inline-flex items-center gap-2 text-xs font-bold text-blue-600 hover:underline">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Download Draft
                            </a>
                        @endif
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900/10 p-4 rounded-xl border border-yellow-100 dark:border-yellow-900/30">
                        <p class="text-xs font-bold text-yellow-700 dark:text-yellow-500 mb-2 uppercase">Catatan Pembimbing</p>
                        @if($log->notes)
                            <p class="text-sm text-slate-700 dark:text-slate-300 whitespace-pre-line">{{ $log->notes }}</p>
                        @else
                            <p class="text-sm text-slate-400 italic">Belum ada catatan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-slate-500">
            Belum ada riwayat bimbingan. Mahasiswa belum mengisi kartu kendali.
        </div>
        @endforelse
    </div>

    <!-- MODAL VALIDASI -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Validasi Bimbingan</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan / Revisi untuk Mahasiswa</label>
                    <textarea wire:model="lecturer_notes" rows="4" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white" placeholder="Contoh: Bab 1 sudah oke, lanjut Bab 2. Perbaiki daftar pustaka."></textarea>
                    @error('lecturer_notes') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-lg text-slate-500 hover:bg-slate-100">Batal</button>
                    <button wire:click="saveValidation" class="px-6 py-2 rounded-lg bg-brand-blue text-white font-bold hover:bg-blue-800 shadow-lg">Simpan & Validasi</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>