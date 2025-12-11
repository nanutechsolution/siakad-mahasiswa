<div>
    <x-slot name="header">Kelola Permohonan Surat</x-slot>

    <!-- Alert -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200 shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 font-bold border border-red-200 shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabs Status -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center mb-6">
        <nav class="flex space-x-2 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
            @foreach(['PENDING' => 'Menunggu', 'COMPLETED' => 'Selesai', 'REJECTED' => 'Ditolak'] as $val => $label)
                <button wire:click="$set('filter_status', '{{ $val }}')" 
                    class="px-4 py-2 text-sm font-bold rounded-lg transition-all {{ $filter_status == $val ? 'bg-white dark:bg-slate-700 text-brand-blue dark:text-white shadow-sm ring-1 ring-black/5' : 'text-slate-500 hover:text-slate-700 hover:bg-white/50 dark:text-slate-400 dark:hover:text-slate-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
        
        <div class="text-xs font-bold text-slate-500 dark:text-slate-400">
            Total: {{ $requests->total() }} Permohonan
        </div>
    </div>

    <!-- Table List -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Mahasiswa</th>
                    <th class="px-6 py-4">Jenis Surat</th>
                    <th class="px-6 py-4">Keperluan</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($requests as $req)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-6 py-4 text-slate-500 text-xs">
                        {{ $req->created_at->format('d M Y') }}
                        <div class="mt-1">{{ $req->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $req->student->user->name }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $req->student->nim }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold uppercase border border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800">
                            {{ str_replace('_', ' ', $req->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300 max-w-xs truncate" title="{{ $req->purpose }}">
                        {{ $req->purpose }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($req->status == 'PENDING')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 border border-yellow-200">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                </span>
                                Baru
                            </span>
                        @elseif($req->status == 'COMPLETED')
                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">Selesai</span>
                        @elseif($req->status == 'REJECTED')
                            <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">Ditolak</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($req->status == 'PENDING')
                            <button wire:click="process('{{ $req->id }}')" class="px-3 py-1.5 bg-brand-blue text-white rounded-lg text-xs font-bold hover:bg-blue-800 shadow-md transition">
                                Proses
                            </button>
                        @elseif($req->status == 'COMPLETED')
                            <div class="flex flex-col items-end gap-1">
                                <span class="text-[10px] text-slate-400">No: {{ $req->letter_number }}</span>
                                {{-- Jika mau fitur admin cetak ulang --}}
                                {{-- <a href="#" class="text-xs text-blue-600 hover:underline">Cetak Ulang</a> --}}
                            </div>
                        @else
                            <span class="text-xs text-slate-400 italic">Closed</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                        Tidak ada permohonan surat pada status ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- MODAL PROSES -->
    @if($isModalOpen && $selectedRequest)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg p-6">
            <div class="flex justify-between items-center mb-6 border-b border-slate-100 dark:border-slate-700 pb-4">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Proses Permohonan</h3>
                <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-red-500 text-2xl">&times;</button>
            </div>
            
            <div class="space-y-4 mb-6">
                <!-- Info Ringkas -->
                <div class="bg-slate-50 dark:bg-slate-700/50 p-4 rounded-lg border border-slate-200 dark:border-slate-600 text-sm">
                    <p class="flex justify-between mb-1">
                        <span class="text-slate-500">Mahasiswa:</span> 
                        <span class="font-bold text-slate-800 dark:text-white">{{ $selectedRequest->student->user->name }}</span>
                    </p>
                    <p class="flex justify-between mb-1">
                        <span class="text-slate-500">NIM:</span> 
                        <span class="font-mono text-slate-800 dark:text-white">{{ $selectedRequest->student->nim }}</span>
                    </p>
                    <p class="flex justify-between mb-1">
                        <span class="text-slate-500">Jenis:</span> 
                        <span class="font-bold text-brand-blue dark:text-brand-gold">{{ str_replace('_', ' ', $selectedRequest->type) }}</span>
                    </p>
                    <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-600">
                        <p class="text-xs text-slate-400 uppercase font-bold mb-1">Keperluan:</p>
                        <p class="text-slate-700 dark:text-slate-300 italic">"{{ $selectedRequest->purpose }}"</p>
                    </div>
                </div>

                <!-- Form Input Nomor Surat (Hanya jika Approve) -->
                <div x-data="{ action: 'approve' }">
                    <div class="flex gap-2 mb-4 p-1 bg-slate-100 dark:bg-slate-700 rounded-lg">
                        <button @click="action = 'approve'" :class="action === 'approve' ? 'bg-white dark:bg-slate-600 text-green-700 shadow-sm' : 'text-slate-500'" class="flex-1 py-1.5 text-xs font-bold rounded-md transition-all">Setujui</button>
                        <button @click="action = 'reject'" :class="action === 'reject' ? 'bg-white dark:bg-slate-600 text-red-700 shadow-sm' : 'text-slate-500'" class="flex-1 py-1.5 text-xs font-bold rounded-md transition-all">Tolak</button>
                    </div>

                    <!-- Form Approve -->
                    <div x-show="action === 'approve'">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Nomor Surat Resmi</label>
                        <input wire:model="letter_number" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white focus:ring-green-500 focus:border-green-500" placeholder="Contoh: 001/BAAK/2025">
                        <p class="text-xs text-slate-400 mt-1">Nomor ini akan dicetak di kop surat.</p>
                        
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                            <button wire:click="approve" class="px-6 py-2 rounded-lg bg-green-600 text-white font-bold hover:bg-green-700 shadow-lg shadow-green-500/30">
                                Terbitkan Surat
                            </button>
                        </div>
                    </div>

                    <!-- Form Reject -->
                    <div x-show="action === 'reject'" style="display: none;">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Alasan Penolakan</label>
                        <textarea wire:model="admin_note" rows="3" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white focus:ring-red-500 focus:border-red-500" placeholder="Contoh: Data belum lengkap..."></textarea>
                        
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                            <button wire:click="reject" class="px-6 py-2 rounded-lg bg-red-600 text-white font-bold hover:bg-red-700 shadow-lg shadow-red-500/30">
                                Tolak Permohonan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>