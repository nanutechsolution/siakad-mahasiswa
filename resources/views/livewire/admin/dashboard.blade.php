<div>
    <x-slot name="header">
        Dashboard
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] p-6 border border-slate-100 dark:border-slate-700 relative overflow-hidden group hover:border-indigo-100 dark:hover:border-indigo-900 transition-all duration-300">
            <div class="absolute right-0 top-0 h-full w-1 bg-indigo-500 rounded-r"></div>
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Mahasiswa Aktif</p>
                    <h3 class="text-3xl font-bold text-slate-800 dark:text-white mt-2">{{ $total_mhs }}</h3>
                </div>
                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-slate-500 dark:text-slate-400">
                <span class="text-green-500 font-bold mr-1">●</span> Data Terupdate
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] p-6 border border-slate-100 dark:border-slate-700 relative overflow-hidden group hover:border-purple-100 dark:hover:border-purple-900 transition-all duration-300">
            <div class="absolute right-0 top-0 h-full w-1 bg-purple-500 rounded-r"></div>
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Dosen Tetap</p>
                    <h3 class="text-3xl font-bold text-slate-800 dark:text-white mt-2">{{ $total_dosen }}</h3>
                </div>
                <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg text-purple-600 dark:text-purple-400 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] p-6 border border-slate-100 dark:border-slate-700 relative overflow-hidden group hover:border-orange-100 dark:hover:border-orange-900 transition-all duration-300">
            <div class="absolute right-0 top-0 h-full w-1 bg-orange-500 rounded-r"></div>
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Program Studi</p>
                    <h3 class="text-3xl font-bold text-slate-800 dark:text-white mt-2">{{ $total_prodi }}</h3>
                </div>
                <div class="p-2 bg-orange-50 dark:bg-orange-900/30 rounded-lg text-orange-600 dark:text-orange-400 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
            </div>
        </div>

        <div class="bg-slate-800 dark:bg-slate-950 rounded-xl shadow-lg p-6 text-white relative overflow-hidden border border-slate-700 dark:border-slate-800">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <p class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Periode Aktif</p>
            <h3 class="text-xl font-bold mt-2 leading-tight">
                {{ $semester_aktif->name ?? 'Tidak Ada' }}
            </h3>
            <div class="mt-4 inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-green-500/20 text-green-300 border border-green-500/30">
                Kode: {{ $semester_aktif->code ?? '-' }}
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 transition-colors duration-300">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 dark:text-white">Aktivitas Sistem</h3>
            <button class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">Lihat Semua</button>
        </div>
        <div class="p-6 text-center text-slate-400 dark:text-slate-500 py-12">
            <svg class="w-12 h-12 mx-auto text-slate-200 dark:text-slate-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
            <p>Belum ada aktivitas terbaru hari ini.</p>
        </div>
    </div>
</div>