<div class="space-y-6 font-sans">
    <x-slot name="header">Validasi KRS Mahasiswa</x-slot>

    <!-- Notifikasi -->
    @if (session()->has('message'))
    <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 shadow-sm animate-fade-in-down">
        {{ session('message') }}
    </div>
    @endif

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex flex-1 w-full gap-2">
            <div class="relative flex-1">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / NIM..." class="w-full pl-10 pr-4 py-2 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue">
                <div class="absolute left-3 top-2.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
            </div>
            <select wire:model.live="filter_status" class="rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue">
                <option value="submitted">Menunggu (Pending)</option>
                <option value="approved">Disetujui</option>
                <option value="draft">Konsep / Revisi</option>
            </select>
        </div>
        <div class="text-right px-4">
            <p class="text-[10px] uppercase font-black text-slate-400 tracking-widest">Periode Aktif</p>
            <p class="text-sm font-bold text-slate-700 dark:text-white">{{ $active_period->name ?? '-' }}</p>
        </div>
    </div>

    <!-- Tabel KRS -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm whitespace-nowrap">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-4">Mahasiswa</th>
                    <th class="px-6 py-4">Program Studi</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 font-medium">
                @forelse($plans as $plan)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 dark:text-white">{{ $plan->student->user->name }}</div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $plan->student->nim }}</div>
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-400">
                        {{ $plan->student->studyProgram->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tight
                            {{ $plan->status->color() === 'green' ? 'bg-green-100 text-green-700' : ($plan->status->color() === 'blue' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600') }}">
                            {{ $plan->status->label() }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="showDetail('{{ $plan->id }}')" class="px-4 py-2 bg-brand-blue text-white rounded-xl text-xs font-bold hover:bg-blue-600 shadow-sm transition">
                            Periksa KRS
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Tidak ada pengajuan KRS yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100 dark:border-slate-700">
            {{ $plans->links() }}
        </div>
    </div>

    <!-- Modal Detail & Validasi -->
    @if($isModalOpen && $selectedPlan)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Header Modal -->
            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center bg-slate-50 dark:bg-slate-700/50">
                <div>
                    <h3 class="text-xl font-bold dark:text-white">Detail KRS: {{ $selectedPlan->student->user->name }}</h3>
                    <p class="text-xs text-slate-500 font-medium uppercase tracking-widest mt-0.5">{{ $selectedPlan->student->nim }} &bull; SMT {{ $selectedPlan->student->entry_year }}</p>
                </div>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <!-- Body Modal -->
            <div class="flex-1 overflow-y-auto p-8 space-y-6">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="text-slate-400 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                        <tr>
                            <th class="pb-4">Mata Kuliah</th>
                            <th class="pb-4 text-center">SKS</th>
                            <th class="pb-4">Kelas & Jadwal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @foreach($selectedPlan->details as $detail)
                        <tr>
                            <td class="py-4">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $detail->courseClass->course->name }}</div>
                                <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $detail->courseClass->course->code }}</div>
                            </td>
                            <td class="py-4 text-center font-bold text-lg text-brand-blue">{{ $detail->courseClass->course->credit_total }}</td>
                            <td class="py-4 text-xs text-slate-600 dark:text-slate-400">
                                <div class="font-bold">Kelas {{ $detail->courseClass->name }}</div>
                                @foreach($detail->courseClass->classSchedules as $sch)
                                    <div>{{ $sch->day_name }}, {{ substr($sch->start_time, 0, 5) }}-{{ substr($sch->end_time, 0, 5) }}</div>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="bg-slate-50 dark:bg-slate-900 p-4 rounded-xl flex justify-between items-center">
                    <span class="font-black text-xs uppercase text-slate-400 tracking-widest">Total Beban SKS</span>
                    <span class="text-2xl font-black text-brand-blue">{{ $selectedPlan->details->sum(fn($d) => $d->courseClass->course->credit_total) }} SKS</span>
                </div>

                <!-- Input Penolakan -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Catatan / Alasan Penolakan (Hanya jika menolak)</label>
                    <textarea wire:model="rejection_notes" rows="3" class="w-full rounded-2xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-red-500" placeholder="Contoh: SKS berlebih atau mata kuliah tidak sesuai prasyarat..."></textarea>
                    @error('rejection_notes') <span class="text-red-500 text-xs font-bold mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Footer Modal -->
            <div class="px-8 py-6 border-t border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-end gap-3 bg-slate-50 dark:bg-slate-700/50">
                <button wire:click="closeModal" class="px-6 py-2.5 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition text-sm">Batal</button>
                <button wire:click="reject" class="px-6 py-2.5 bg-red-100 text-red-700 rounded-xl font-bold hover:bg-red-200 transition text-sm">Tolak / Revisi</button>
                <button wire:click="approve" class="px-8 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-600 shadow-lg shadow-blue-900/20 transition text-sm">Setujui KRS</button>
            </div>
        </div>
    </div>
    @endif
</div>