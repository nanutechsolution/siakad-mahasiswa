<div class="space-y-6 font-sans">
    <x-slot name="header">Plotting Dosen Wali</x-slot>

    @if (session()->has('message'))
    <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 shadow-sm animate-fade-in-down">
        {{ session('message') }}
    </div>
    @endif

    <!-- Toolbar & Filters -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div>
            <label class="block text-xs font-black uppercase text-slate-400 mb-2">Program Studi</label>
            <select wire:model.live="filter_prodi" class="w-full rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue shadow-sm">
                @foreach($prodis as $p)
                <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-black uppercase text-slate-400 mb-2">Angkatan</label>
            <input type="number" wire:model.live="filter_angkatan" class="w-full rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white text-sm focus:ring-brand-blue shadow-sm">
        </div>
        <div class="flex items-end pb-2">
            <label class="inline-flex items-center cursor-pointer select-none">
                <input type="checkbox" wire:model.live="show_has_advisor" class="rounded text-brand-blue h-5 w-5 border-slate-300">
                <span class="ml-3 text-sm font-bold text-slate-600 dark:text-slate-300">Tampilkan yang sudah ada PA</span>
            </label>
        </div>
    </div>

    <!-- Mass Action Bar (Floating Style) -->
    @if(count($selected_students) > 0)
    <div class="fixed bottom-8 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-2xl px-4">
        <div class="bg-slate-900 text-white rounded-3xl p-4 shadow-2xl flex flex-col md:flex-row items-center gap-4">
            <div class="px-4 border-r border-slate-700 hidden md:block">
                <p class="text-[10px] uppercase font-black text-slate-500">Terpilih</p>
                <p class="text-lg font-black">{{ count($selected_students) }} <span class="text-xs">Mhs</span></p>
            </div>
            <div class="flex-1 w-full">
                <select wire:model="selected_lecturer" class="w-full bg-slate-800 border-slate-700 text-white rounded-xl text-sm focus:ring-blue-500">
                    <option value="">-- Pilih Dosen Wali --</option>
                    @foreach($lecturers as $l)
                    <option value="{{ $l->id }}">{{ $l->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button wire:click="save" class="flex-1 md:flex-none px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-600 transition text-sm">Plotting</button>
                <button wire:click="detach" wire:confirm="Lepas dosen wali dari mahasiswa terpilih?" class="px-3 py-2.5 bg-red-500/20 text-red-400 rounded-xl hover:bg-red-500/30 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Table -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="select_all" class="rounded text-brand-blue h-4 w-4">
                        </th>
                        <th class="px-6 py-4">Mahasiswa</th>
                        <th class="px-6 py-4">Dosen Wali (Saat Ini)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($students as $student)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" wire:model.live="selected_students" value="{{ $student->id }}" class="rounded text-brand-blue h-4 w-4">
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800 dark:text-white">{{ $student->user->name }}</div>
                            <div class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">{{ $student->nim }} &bull; {{ $student->entry_year }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($student->academicAdvisor)
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                    <span class="font-medium text-slate-700 dark:text-slate-300 text-xs">{{ $student->academicAdvisor->user->name }}</span>
                                </div>
                            @else
                                <span class="text-slate-300 italic text-xs">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ $student->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $student->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Data mahasiswa tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700">
            {{ $students->links() }}
        </div>
    </div>
</div>