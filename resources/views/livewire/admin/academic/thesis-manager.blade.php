<div>
    <x-slot name="header">Manajemen Skripsi & TA</x-slot>

    <!-- Alert -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200">
            ✅ {{ session('message') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <div class="flex gap-2">
            <select wire:model.live="filter_status" class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white text-sm">
                <option value="PROPOSED">Baru Diajukan (Proposed)</option>
                <option value="APPROVED">Disetujui / Bimbingan</option>
                <option value="REJECTED">Ditolak</option>
                <option value="COMPLETED">Selesai / Lulus</option>
            </select>
            
            <select wire:model.live="filter_prodi" class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white text-sm">
                <option value="">Semua Prodi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Judul / Mahasiswa..." class="rounded-lg border-slate-300 w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Mahasiswa</th>
                    <th class="px-6 py-4 w-1/2">Judul Proposal</th>
                    <th class="px-6 py-4 text-center">File</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($theses as $t)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $t->student->user->name }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $t->student->nim }}</div>
                        <div class="text-xs text-brand-blue mt-1">{{ $t->student->study_program->code }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800 dark:text-slate-200 line-clamp-2">{{ $t->title }}</div>
                        <div class="text-[10px] text-slate-400 mt-1">Diajukan: {{ $t->created_at->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($t->proposal_file)
                            <a href="{{ asset('storage/'.$t->proposal_file) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                PDF
                            </a>
                        @else - @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusColors = [
                                'PROPOSED' => 'bg-yellow-100 text-yellow-800',
                                'APPROVED' => 'bg-green-100 text-green-800',
                                'REJECTED' => 'bg-red-100 text-red-800',
                                'COMPLETED' => 'bg-blue-100 text-blue-800',
                            ];
                        @endphp
                        <span class="{{ $statusColors[$t->status] ?? 'bg-gray-100' }} px-2 py-1 rounded-full text-[10px] font-bold uppercase">
                            {{ $t->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="showDetail('{{ $t->id }}')" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-brand-blue shadow-md transition">
                            Detail / Proses
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada pengajuan judul.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $theses->links() }}</div>
    </div>

    <!-- MODAL DETAIL & APPROVAL -->
    @if($isModalOpen && $selectedThesis)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-3xl overflow-hidden max-h-[90vh] flex flex-col">
            
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Review Proposal Skripsi</h3>
                <p class="text-sm text-slate-500">{{ $selectedThesis->student->user->name }} ({{ $selectedThesis->student->nim }})</p>
            </div>
            
            <div class="p-6 overflow-y-auto flex-1 space-y-6">
                
                <!-- Info Judul -->
                <div class="bg-slate-50 dark:bg-slate-700/50 p-4 rounded-lg border border-slate-200 dark:border-slate-600">
                    <p class="text-xs font-bold text-slate-400 uppercase mb-1">Judul</p>
                    <p class="font-bold text-slate-800 dark:text-white text-lg leading-snug">{{ $selectedThesis->title }}</p>
                    
                    <p class="text-xs font-bold text-slate-400 uppercase mt-4 mb-1">Abstrak</p>
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">{{ $selectedThesis->abstract }}</p>
                </div>

                <!-- Form Plotting (Hanya muncul jika belum selesai/ditolak) -->
                @if($selectedThesis->status != 'REJECTED')
                <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                    <h4 class="font-bold text-brand-blue dark:text-brand-gold mb-3 uppercase text-xs">Plotting Pembimbing</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Pembimbing Utama (1)</label>
                            <select wire:model="supervisor1_id" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                <option value="">-- Pilih Dosen --</option>
                                @foreach($lecturers as $l)
                                    <option value="{{ $l->id }}">{{ $l->user->name }}</option>
                                @endforeach
                            </select>
                            @error('supervisor1_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Pembimbing Pendamping (2)</label>
                            <select wire:model="supervisor2_id" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                <option value="">-- Tidak Ada / Pilih Dosen --</option>
                                @foreach($lecturers as $l)
                                    <option value="{{ $l->id }}">{{ $l->user->name }}</option>
                                @endforeach
                            </select>
                            @error('supervisor2_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <div class="p-6 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                <button wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-lg text-slate-500 hover:bg-slate-100">Batal</button>
                
                @if($selectedThesis->status == 'PROPOSED')
                    <button wire:click="reject" wire:confirm="Tolak judul ini? Mahasiswa harus mengajukan ulang." class="px-4 py-2 rounded-lg border border-red-200 text-red-600 font-bold hover:bg-red-50">
                        Tolak Pengajuan
                    </button>
                    <button wire:click="approve" wire:confirm="Setujui judul dan tetapkan pembimbing?" class="px-6 py-2 rounded-lg bg-green-600 text-white font-bold hover:bg-green-700 shadow-lg shadow-green-500/30">
                        SETUJUI & PLOT PEMBIMBING
                    </button>
                @elseif($selectedThesis->status == 'APPROVED')
                    <button wire:click="approve" class="px-6 py-2 rounded-lg bg-brand-blue text-white font-bold hover:bg-blue-800">
                        Update Pembimbing
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>