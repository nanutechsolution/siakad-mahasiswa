<div class="mx-auto max-w-7xl space-y-6">
    <x-slot name="header">Validasi KRS (Dosen Wali)</x-slot>

    <!-- Header Info -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Mahasiswa Bimbingan</h1>
            <p class="text-slate-500">Daftar mahasiswa yang mengajukan KRS untuk semester ini.</p>
        </div>
        
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / NIM..." class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white w-full md:w-64">
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg font-bold">
            ✅ {{ session('message') }}
        </div>
    @endif

    <!-- List Card Mahasiswa -->
    @if(isset($students) && $students->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($students as $mhs)
            @php
                // Cek status mayoritas KRS
                $status = $mhs->study_plans->first()->status ?? App\Enums\KrsStatus::DRAFT;
                $totalSks = $mhs->study_plans->sum(fn($p) => $p->classroom->course->credit_total);
            @endphp

            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:border-brand-blue transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold">
                            {{ substr($mhs->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white leading-tight">{{ $mhs->user->name }}</h3>
                            <p class="text-xs text-slate-500 font-mono">{{ $mhs->nim }}</p>
                        </div>
                    </div>
                    
                    @if($status == App\Enums\KrsStatus::APPROVED)
                        <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full">ACC</span>
                    @else
                        <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-1 rounded-full animate-pulse">BUTUH CEK</span>
                    @endif
                </div>

                <div class="flex justify-between items-center text-sm text-slate-600 dark:text-slate-400 mb-6">
                    <span>Total Beban:</span>
                    <span class="font-bold text-brand-blue dark:text-brand-gold">{{ $totalSks }} SKS</span>
                </div>

                <button wire:click="showDetail('{{ $mhs->id }}')" class="w-full py-2 rounded-lg bg-slate-900 dark:bg-slate-700 text-white text-sm font-bold hover:bg-brand-blue transition-colors shadow-lg shadow-slate-900/20">
                    Periksa & Validasi
                </button>
            </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $students->links() }}</div>
    @else
        <div class="text-center py-12 bg-white dark:bg-slate-800 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
            <p class="text-slate-500">Belum ada mahasiswa bimbingan yang mengajukan KRS.</p>
        </div>
    @endif

    <!-- MODAL DETAIL -->
    @if($isModalOpen && $selectedStudent)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Rencana Studi</h3>
                    <p class="text-sm text-slate-500">{{ $selectedStudent->user->name }} ({{ $selectedStudent->nim }})</p>
                </div>
                <button wire:click="$set('isModalOpen', false)" class="text-slate-400 hover:text-red-500">&times;</button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">Matkul</th>
                            <th class="px-4 py-3">Kelas</th>
                            <th class="px-4 py-3 text-center">SKS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($studentPlans as $plan)
                        <tr>
                            <td class="px-4 py-3 font-medium text-slate-900 dark:text-white">
                                {{ $plan->classroom->course->name }}
                                <div class="text-xs text-slate-400">{{ $plan->classroom->course->code }}</div>
                            </td>
                            <td class="px-4 py-3">{{ $plan->classroom->name }}</td>
                            <td class="px-4 py-3 text-center font-bold">{{ $plan->classroom->course->credit_total }}</td>
                        </tr>
                        @endforeach
                        <tr class="bg-slate-50 dark:bg-slate-900 font-bold">
                            <td colspan="2" class="px-4 py-3 text-right">TOTAL SKS</td>
                            <td class="px-4 py-3 text-center text-brand-blue">{{ $studentPlans->sum(fn($p)=>$p->classroom->course->credit_total) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex justify-end gap-3">
                <button wire:click="reject" wire:confirm="Kembalikan ke Draft? Mahasiswa harus mengajukan ulang." 
                        class="px-4 py-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 font-bold text-sm transition-colors">
                    Tolak / Revisi
                </button>
                <button wire:click="approve" wire:confirm="Setujui Rencana Studi ini?" 
                        class="px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-bold text-sm shadow-lg shadow-green-500/30 transition-colors">
                    SETUJUI (ACC)
                </button>
            </div>
        </div>
    </div>
    @endif
</div>