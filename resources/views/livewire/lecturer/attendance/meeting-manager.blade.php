<div class="mx-auto max-w-6xl space-y-6 font-sans text-slate-600 dark:text-slate-300">
    <x-slot name="header">Presensi Perkuliahan</x-slot>

    <!-- Header Kelas -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 gap-4">
        <div>
            <span class="bg-brand-blue text-white text-xs px-2 py-1 rounded font-bold mb-2 inline-block">Kelas {{ $classroom->name }}</span>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $classroom->course->name }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">{{ $classroom->course->code }} • {{ $classroom->schedules->first()->day ?? '-' }}</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="showRecap" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl font-bold hover:bg-slate-50 transition shadow-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Lihat Rekap
            </button>
            <a href="{{ route('lecturer.print.attendance', $classroom->id) }}" target="_blank" class="px-5 py-2.5 bg-slate-900 text-white border border-slate-900 rounded-xl font-bold hover:bg-slate-700 transition shadow-lg flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
    Cetak PDF
</a>
            <button wire:click="createMeeting" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition shadow-lg flex items-center gap-2 dark:bg-brand-blue dark:hover:bg-blue-800">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Buat Pertemuan
            </button>
        </div>
    </div>

    <!-- Alert -->
    @if (session()->has('message'))
        <div class="p-4 bg-green-100 border border-green-200 text-green-700 rounded-xl font-bold shadow-sm flex items-center gap-2 dark:bg-green-900/30 dark:border-green-800 dark:text-green-400">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- KOLOM KIRI: LIST PERTEMUAN -->
        <div class="lg:col-span-1 space-y-4">
            <h3 class="font-bold text-slate-800 dark:text-white mb-2">Riwayat Pertemuan</h3>
            
            <div class="max-h-[500px] overflow-y-auto pr-2 custom-scrollbar space-y-3">
                @foreach($meetings as $m)
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border transition-all cursor-pointer hover:shadow-md 
                            {{ $selected_meeting && $selected_meeting->id == $m->id ? 'border-brand-blue ring-1 ring-brand-blue' : 'border-slate-200 dark:border-slate-700' }}" 
                     wire:click="showDetail('{{ $m->id }}')">
                    
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs font-bold text-slate-400 uppercase">Pertemuan {{ $m->meeting_no }}</span>
                        <span class="text-xs font-mono text-slate-500">{{ $m->meeting_date->format('d/m/Y') }}</span>
                    </div>
                    
                    @if($m->is_open)
                        <div class="bg-green-100 text-green-700 p-3 rounded-lg text-center mb-3 border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800">
                            <p class="text-[10px] font-bold uppercase">Token Absen</p>
                            <p class="text-2xl font-black tracking-widest">{{ $m->token }}</p>
                        </div>
                        <button wire:click.stop="closeAttendance('{{ $m->id }}')" class="w-full py-1.5 bg-red-50 text-red-600 rounded text-xs font-bold hover:bg-red-100 border border-red-200 transition dark:bg-red-900/20 dark:text-red-400 dark:border-red-800 dark:hover:bg-red-900/40">
                            Tutup Sesi
                        </button>
                    @else
                        <button wire:click.stop="openAttendance('{{ $m->id }}')" class="w-full py-1.5 bg-blue-50 text-blue-600 rounded text-xs font-bold hover:bg-blue-100 border border-blue-200 transition dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800 dark:hover:bg-blue-900/40">
                            Buka Sesi
                        </button>
                    @endif
                </div>
                @endforeach
            </div>
            
            @if($meetings->isEmpty())
                <div class="text-center p-6 text-slate-400 text-sm border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                    Belum ada pertemuan dibuat.
                </div>
            @endif
        </div>

        <!-- KOLOM KANAN: DETAIL / REKAP -->
        <div class="lg:col-span-2">
            
            <!-- TAMPILAN REKAP -->
            @if($show_recap)
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800 dark:text-white">Rekapitulasi Kehadiran</h3>
                        <span class="text-xs font-bold text-slate-500">Total Pertemuan: {{ $meetings->count() }}</span>
                    </div>

                    <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-100 dark:bg-slate-700 text-slate-500 font-bold text-xs uppercase sticky top-0">
                                <tr>
                                    <th class="px-4 py-3">Mahasiswa</th>
                                    <th class="px-4 py-3 text-center">H</th>
                                    <th class="px-4 py-3 text-center">I</th>
                                    <th class="px-4 py-3 text-center">S</th>
                                    <th class="px-4 py-3 text-center">A</th>
                                    <th class="px-4 py-3 text-center">% Hadir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($recap_data as $data)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-800 dark:text-white">{{ $data['name'] }}</div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $data['nim'] }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-green-600 dark:text-green-400">{{ $data['hadir'] }}</td>
                                    <td class="px-4 py-3 text-center text-yellow-600 dark:text-yellow-500">{{ $data['izin'] }}</td>
                                    <td class="px-4 py-3 text-center text-yellow-600 dark:text-yellow-500">{{ $data['sakit'] }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-red-500 dark:text-red-400">{{ $data['alpha'] }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 rounded text-xs font-bold {{ $data['percentage'] < 75 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                                            {{ $data['percentage'] }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- TAMPILAN DETAIL PERTEMUAN -->
            @elseif($selected_meeting)
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="p-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800 dark:text-white">Presensi Pertemuan {{ $selected_meeting->meeting_no }}</h3>
                        
                        <!-- Statistik Kehadiran -->
                        <div class="flex gap-2 text-xs">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded font-bold border border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800">H: {{ $attendance_list->where('status', 'H')->count() }}</span>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded font-bold border border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800">I/S: {{ $attendance_list->whereIn('status', ['I','S'])->count() }}</span>
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded font-bold border border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800">A: {{ $attendance_list->where('status', 'A')->count() }}</span>
                        </div>
                    </div>

                    <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-100 dark:bg-slate-700 text-slate-500 font-bold text-xs uppercase sticky top-0">
                                <tr>
                                    <th class="px-4 py-3">Mahasiswa</th>
                                    <th class="px-4 py-3 text-center">Waktu</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach($attendance_list as $att)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-800 dark:text-white">{{ $att->student->user->name }}</div>
                                        <div class="text-xs text-slate-400 font-mono">{{ $att->student->nim }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-xs font-mono text-slate-500">
                                        {{ $att->check_in_at ? $att->check_in_at->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex bg-slate-100 dark:bg-slate-700 rounded-lg p-1 gap-1">
                                            @foreach(['H', 'I', 'S', 'A'] as $s)
                                                <button wire:click="updateStatus('{{ $att->id }}', '{{ $s }}')"
                                                    class="w-6 h-6 flex items-center justify-center text-xs font-bold rounded-md transition-all {{ $att->status == $s 
                                                        ? ($s=='H'?'bg-green-500 text-white shadow-sm':($s=='A'?'bg-red-500 text-white shadow-sm':'bg-yellow-500 text-white shadow-sm')) 
                                                        : 'text-slate-400 hover:bg-white dark:text-slate-500 dark:hover:bg-slate-600' }}">
                                                    {{ $s }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            <!-- STATE KOSONG -->
            @else
                <div class="h-full flex flex-col items-center justify-center text-slate-400 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 min-h-[300px]">
                    <div class="w-16 h-16 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center mb-4 shadow-sm">
                        <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" /></svg>
                    </div>
                    <p class="font-medium">Pilih pertemuan di sebelah kiri atau Buat Pertemuan Baru.</p>
                </div>
            @endif
        </div>
    </div>
</div>