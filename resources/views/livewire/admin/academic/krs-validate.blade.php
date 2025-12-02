<div>
    <x-slot name="header">Validasi KRS (Admin)</x-slot>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-6 flex flex-col md:flex-row gap-4 justify-between">
        <div class="flex gap-4">
            <select wire:model.live="filter_status"
                class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                <option value="Pending">Menunggu Validasi (Pending)</option>
                <option value="APPROVED">Sudah Disetujui (Approved)</option> {{-- Perhatikan Case Sensitive --}}
                <option value="DRAFT">Draft (Belum Diajukan)</option>
            </select>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / NIM..."
                class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white w-64">
        </div>
    </div>

    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Mahasiswa</th>
                    <th class="px-6 py-4">Prodi</th>
                    <th class="px-6 py-4 text-center">Total Mata Kuliah</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($students as $mhs)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $mhs->user->name }}</div>
                            <div class="text-xs text-slate-400 font-mono">{{ $mhs->nim }}</div>
                        </td>
                        <td class="px-6 py-4">
                            {{ $mhs->study_program->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-center font-bold text-brand-blue dark:text-brand-gold">
                            {{-- Hitung jumlah matkul yg statusnya sesuai filter --}}
                            {{ $mhs->study_plans()->where('status', $filter_status)->count() }} Matkul
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="showDetail('{{ $mhs->id }}')"
                                class="bg-brand-blue text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-blue-800 transition">
                                Periksa KRS
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                            Tidak ada pengajuan KRS dengan status <strong>{{ $filter_status }}</strong>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $students->links() }}
        </div>
    </div>

    @if ($isModalOpen && $selectedStudent)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
            <div
                class="w-full max-w-2xl bg-white dark:bg-slate-800 rounded-xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">

                <div
                    class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Detail KRS Mahasiswa</h3>
                        <p class="text-sm text-slate-500">{{ $selectedStudent->user->name }}
                            ({{ $selectedStudent->nim }})</p>
                    </div>
                    <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-red-500">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs uppercase text-slate-400 bg-slate-50 dark:bg-slate-700">
                            <tr>
                                <th class="px-4 py-2">Mata Kuliah</th>
                                <th class="px-4 py-2">Kelas</th>
                                <th class="px-4 py-2 text-center">SKS</th>
                                <th class="px-4 py-2 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach ($studentPlans as $plan)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-800 dark:text-white">
                                            {{ $plan->classroom->course->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $plan->classroom->course->code }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $plan->classroom->name }}</td>
                                    <td class="px-4 py-3 text-center font-bold">
                                        {{ $plan->classroom->course->credit_total }}</td>
                                    <td class="px-4 py-3 text-center">
                                        @if ($plan->status == 'APPROVED')
                                            <span class="text-green-600 font-bold text-xs">ACC</span>
                                        @elseif($plan->status == 'Pending')
                                            <span class="text-yellow-600 font-bold text-xs">PENDING</span>
                                        @else
                                            <span class="text-slate-400 text-xs">DRAFT</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-50 dark:bg-slate-900 font-bold">
                                <td colspan="2" class="px-4 py-3 text-right">TOTAL SKS</td>
                                <td class="px-4 py-3 text-center text-brand-blue">
                                    {{ $studentPlans->sum(fn($p) => $p->classroom->course->credit_total) }}
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex justify-end gap-3">
                    @if ($filter_status != 'APPROVED')
                        {{-- Tombol muncul jika sedang melihat yang Pending/Draft --}}
                        <button wire:click="reject"
                            wire:confirm="Kembalikan KRS ke Draft? Mahasiswa harus mengajukan ulang."
                            class="px-4 py-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 font-bold text-sm">
                            Tolak / Revisi
                        </button>
                        <button wire:click="approve" wire:confirm="Setujui KRS ini?"
                            class="px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-bold text-sm shadow-lg shadow-green-500/30">
                            SETUJUI SEMUA (ACC)
                        </button>
                    @else
                        {{-- Jika sudah Approved, tombol Batalkan --}}
                        <button wire:click="reject" wire:confirm="Batalkan persetujuan?"
                            class="text-xs text-red-500 underline hover:text-red-700">Batalkan Persetujuan</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
