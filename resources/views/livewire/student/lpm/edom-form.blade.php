<div class="mx-auto max-w-4xl space-y-6">
    <x-slot name="header">Pengisian Kuesioner</x-slot>
    <!-- Info Kelas -->
    <div
        class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-center sticky top-4 z-30">
        <div>
            <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">Mata Kuliah</p>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">{{ $classroom->course->name }}</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">Dosen: <span
                    class="font-semibold text-brand-blue dark:text-brand-gold">{{ $classroom->lecturer->user->name }}</span>
            </p>
        </div>
        <div class="text-right">
            <a href="{{ route('student.edom.list') }}"
                class="text-xs font-bold text-slate-400 hover:text-slate-600">BATAL</a>
        </div>
    </div>
    <form wire:submit.prevent="store" class="space-y-8 pb-12">
        @foreach ($questions as $category => $list)
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm overflow-hidden">
                <div class="bg-slate-50 dark:bg-slate-700 px-6 py-4 border-b border-slate-100 dark:border-slate-600">
                    <h3 class="font-bold text-slate-800 dark:text-white uppercase tracking-wider text-sm">
                        {{ $category }}</h3>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach ($list as $q)
                        <div class="p-6 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <p class="text-slate-700 dark:text-slate-300 font-medium mb-3">{{ $loop->iteration }}.
                                {{ $q->question_text }}</p>

                            <div class="flex items-center gap-4">
                                <!-- Pilihan Rating 1-5 -->
                                @for ($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer group">
                                        <input type="radio" wire:model="answers.{{ $q->id }}"
                                            value="{{ $i }}" class="peer sr-only">
                                        <div
                                            class="w-10 h-10 rounded-full border-2 border-slate-200 dark:border-slate-600 flex items-center justify-center text-slate-400 font-bold transition-all peer-checked:border-brand-blue peer-checked:bg-brand-blue peer-checked:text-white peer-checked:shadow-lg group-hover:border-brand-blue/50">
                                            {{ $i }}
                                        </div>
                                        <div
                                            class="text-[10px] text-center mt-1 text-slate-300 peer-checked:text-brand-blue font-medium opacity-0 peer-checked:opacity-100">
                                            {{ $i == 1 ? 'Buruk' : ($i == 5 ? 'Sangat Baik' : '') }}
                                        </div>
                                    </label>
                                @endfor
                            </div>
                            @error("answers.{$q->id}")
                                <span class="text-xs text-red-500 mt-2 block">Wajib diisi.</span>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex justify-end pt-4">
            <button type="submit"
                class="px-8 py-3 bg-brand-blue text-white rounded-xl font-bold shadow-xl shadow-blue-900/20 hover:bg-blue-800 hover:scale-105 transition-all transform">
                Kirim Evaluasi
            </button>
        </div>
    </form>
</div>
