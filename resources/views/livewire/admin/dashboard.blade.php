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

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 dark:text-white">Aktivitas Sistem Terbaru</h3>
            <!-- Tombol Lihat Semua (Bisa dibuatkan halaman khusus log nanti) -->
            <button class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 font-medium">Real-time Log</button>
        </div>
        
        <div class="p-0">
            @if($recent_activities->isEmpty())
                <div class="p-8 text-center text-slate-400">
                    <svg class="w-12 h-12 mx-auto text-slate-200 dark:text-slate-700 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <p>Belum ada aktivitas tercatat hari ini.</p>
                </div>
            @else
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-3">User</th>
                            <th class="px-6 py-3">Aksi</th>
                            <th class="px-6 py-3">Keterangan</th>
                            <th class="px-6 py-3 text-right">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($recent_activities as $log)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center font-bold text-xs text-slate-600 dark:text-slate-300">
                                        {{ substr($log->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-white">{{ $log->user->name ?? 'System' }}</p>
                                        <p class="text-[10px] text-slate-500 uppercase">{{ $log->user->role ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                @php
                                    $badgeColor = match($log->action) {
                                        'CREATE', 'INSERT' => 'bg-green-100 text-green-700',
                                        'UPDATE', 'EDIT' => 'bg-blue-100 text-blue-700',
                                        'DELETE' => 'bg-red-100 text-red-700',
                                        'LOGIN' => 'bg-indigo-100 text-indigo-700',
                                        'LOGOUT' => 'bg-slate-100 text-slate-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $badgeColor }}">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-slate-600 dark:text-slate-300 truncate max-w-xs">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-3 text-right text-slate-400 text-xs">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>