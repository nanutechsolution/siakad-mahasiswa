<div class="max-w-5xl mx-auto font-sans space-y-6">
    <x-slot name="header">Kelola Prasyarat</x-slot>

    <!-- Header Info Course -->
    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">
                {{ $currentCourse->course->name }} 
                <span class="text-slate-400 font-normal text-lg">({{ $currentCourse->course->code }})</span>
            </h2>
            <p class="text-slate-500 mt-1">
                Semester <span class="font-bold text-slate-700 dark:text-slate-300">{{ $currentCourse->semester }}</span> 
                &bull; Kurikulum {{ $currentCourse->curriculum->year }}
            </p>
        </div>
        <div>
            <a href="{{ route('admin.academic.curriculum-courses.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-bold transition">
                &larr; Kembali
            </a>
        </div>
    </div>

    @if (session()->has('message'))
    <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200">
        {{ session('message') }}
    </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="font-bold text-lg">Pilih Matakuliah Syarat</h3>
                <p class="text-sm text-slate-500">Centang matakuliah yang HARUS lulus sebelum mengambil matakuliah ini.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700/50 uppercase text-[10px] text-slate-500 font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4 w-10 text-center">Pilih</th>
                            <th class="px-6 py-4">Matakuliah Kandidat</th>
                            <th class="px-6 py-4 text-center">Semester</th>
                            <th class="px-6 py-4 w-40">Min. Nilai</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($availableCourses as $c)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition {{ isset($selectedCourses[$c->id]) && $selectedCourses[$c->id] ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                            <td class="px-6 py-4 text-center">
                                <!-- FIX: Tambahkan .live agar reaktif saat dicentang -->
                                <input type="checkbox" 
                                    wire:model.live="selectedCourses.{{ $c->id }}" 
                                    class="w-5 h-5 text-brand-blue rounded border-slate-300 focus:ring-brand-blue cursor-pointer">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $c->course->name }}</div>
                                <div class="text-xs text-slate-400">{{ $c->course->code }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded-full text-xs font-bold">{{ $c->semester }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <!-- Dropdown Nilai akan langsung muncul karena .live di checkbox -->
                                @if(isset($selectedCourses[$c->id]) && $selectedCourses[$c->id])
                                <select wire:model="grades.{{ $c->id }}" class="w-full py-1.5 px-3 rounded-lg border-slate-300 text-sm focus:ring-brand-blue">
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                    <option value="D">D</option>
                                </select>
                                @else
                                <span class="text-slate-300 text-xs italic">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400 italic">
                                Tidak ada matakuliah lain di semester sebelumnya yang bisa dijadikan syarat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                <button type="submit" 
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="px-8 py-3 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 shadow-lg transition flex items-center gap-2">
                    <svg wire:loading.remove wire:target="save" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <svg wire:loading wire:target="save" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>