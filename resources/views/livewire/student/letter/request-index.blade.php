<div class="mx-auto max-w-5xl space-y-6 font-sans">
    <x-slot name="header">Layanan Surat Menyurat</x-slot>

    <!-- Header Action -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Arsip Permohonan</h2>
            <p class="text-sm text-slate-500">Ajukan surat keterangan resmi dari kampus secara online.</p>
        </div>
        <button wire:click="$set('isModalOpen', true)" class="px-5 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 transition shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Buat Pengajuan Baru
        </button>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-green-100 text-green-700 rounded-xl font-bold border border-green-200 flex items-center gap-2 shadow-sm">
            ✅ {{ session('message') }}
        </div>
    @endif

    <!-- List Request -->
    <div class="grid gap-4">
        @forelse($requests as $req)
        <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col md:flex-row justify-between gap-4">
            
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-indigo-50 text-indigo-700 text-xs font-black px-2 py-1 rounded uppercase border border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">
                        {{ str_replace('_', ' ', $req->type) }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">{{ $req->created_at->format('d M Y') }}</span>
                </div>
                <p class="text-slate-800 dark:text-white font-bold">{{ $req->purpose }}</p>
                @if($req->admin_note)
                    <div class="mt-2 p-3 bg-red-50 text-red-600 text-xs rounded-lg border border-red-100">
                        <strong>Catatan Admin:</strong> {{ $req->admin_note }}
                    </div>
                @endif
                @if($req->status == 'COMPLETED' && $req->letter_number)
                     <p class="text-xs text-green-600 font-mono mt-2 font-bold">No. Surat: {{ $req->letter_number }}</p>
                @endif
            </div>

            <div class="flex flex-col items-end gap-2">
                @if($req->status == 'PENDING')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold animate-pulse">Menunggu</span>
                @elseif($req->status == 'PROCESSED')
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold">Diproses</span>
                @elseif($req->status == 'COMPLETED')
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Selesai</span>
                    <a href="{{ route('student.print.letter', $req->id) }}" target="_blank" class="flex items-center gap-1 text-xs font-bold text-brand-blue hover:underline">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download PDF
                    </a>
                @elseif($req->status == 'REJECTED')
                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Ditolak</span>
                @endif
            </div>

        </div>
        @empty
        <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
            <p class="text-slate-500">Belum ada riwayat pengajuan surat.</p>
        </div>
        @endforelse
    </div>
    
    {{ $requests->links() }}

    <!-- MODAL FORM -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="w-full max-w-lg bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Ajukan Surat Baru</h3>
            </div>
            
            <form wire:submit.prevent="store" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Surat</label>
                    <select wire:model="type" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white">
                        <option value="AKTIF_KULIAH">Surat Keterangan Aktif Kuliah</option>
                        <option value="MAGANG">Surat Pengantar Magang / KP</option>
                        <option value="CUTI">Surat Izin Cuti Akademik</option>
                        <option value="PENELITIAN">Surat Izin Penelitian Skripsi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Keperluan / Tujuan</label>
                    <textarea wire:model="purpose" rows="3" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white" placeholder="Contoh: Persyaratan Tunjangan Gaji Orang Tua (PNS)"></textarea>
                    @error('purpose') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
                
                <div class="pt-4 flex justify-end gap-2 border-t dark:border-slate-700">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded-lg bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-lg">Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>