<div class="mx-auto max-w-lg space-y-8 font-sans">
    <x-slot name="header">Presensi Digital</x-slot>

    <!-- FORM INPUT TOKEN -->
    <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 p-8 text-center text-white shadow-2xl">
        <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-brand-blue/50 rounded-full blur-[60px]"></div>
        <div class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-brand-gold/20 rounded-full blur-[60px]"></div>
        
        <div class="relative z-10">
            <h2 class="text-2xl font-black mb-2">Input Token Presensi</h2>
            <p class="text-slate-400 text-sm mb-8">Masukkan 6 digit kode yang diberikan Dosen.</p>

            @if (session()->has('error'))
                <div class="mb-6 p-3 bg-red-500/20 border border-red-500/50 text-red-200 rounded-xl text-sm font-bold animate-pulse">
                    ⚠️ {{ session('error') }}
                </div>
            @endif
            @if (session()->has('success'))
                <div class="mb-6 p-3 bg-green-500/20 border border-green-500/50 text-green-200 rounded-xl text-sm font-bold">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form wire:submit.prevent="submit">
                <input type="text" wire:model="token" 
                       class="w-full bg-slate-800 border-2 border-slate-700 text-center text-4xl font-mono font-black text-white rounded-2xl py-4 focus:ring-4 focus:ring-brand-blue/50 focus:border-brand-blue transition-all tracking-[0.5em] placeholder-slate-600 uppercase"
                       placeholder="XXXXXX" maxlength="6" autofocus>
                
                <button type="submit" 
                        class="mt-6 w-full py-4 bg-brand-blue hover:bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-600/30 transition-transform active:scale-95 flex items-center justify-center gap-2">
                    <span wire:loading.remove>Check In Sekarang</span>
                    <span wire:loading>Memvalidasi...</span>
                    <svg wire:loading.remove class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- RIWAYAT HARI INI -->
    <div>
        <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Riwayat Hadir Hari Ini
        </h3>

        <div class="space-y-3">
            @forelse($today_logs as $log)
                <div class="bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $log->class_meeting->classroom->course->name }}</p>
                        <p class="text-xs text-slate-500">Pertemuan Ke-{{ $log->class_meeting->meeting_no }}</p>
                    </div>
                    <div class="text-right">
                        <span class="bg-green-100 text-green-700 text-xs font-black px-3 py-1 rounded-full uppercase">
                            {{ $log->check_in_at->format('H:i') }} WIB
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-slate-400 bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                    Belum ada presensi hari ini.
                </div>
            @endforelse
        </div>
    </div>
</div>