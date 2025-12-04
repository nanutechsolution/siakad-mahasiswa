<div class="mx-auto max-w-4xl space-y-5 font-sans">
    <x-slot name="header">Pengisian Kuesioner</x-slot>

    <!-- 1. HEADER INFO (Sticky & Ringkas) -->
    <!-- Sticky agar info dosen tetap terlihat saat scroll ke bawah -->
    <div class="sticky top-0 z-40 -mx-4 md:-mx-6 px-4 md:px-6 py-3 bg-[#F8FAFC]/95 dark:bg-[#020617]/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800/80 transition-all">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">
                    <span class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-400">
                        {{ $classroom->course->code }}
                    </span>
                    <span class="truncate">Dosen: {{ $classroom->lecturer->user->name }}</span>
                </div>
                <h2 class="text-base md:text-lg font-black text-slate-900 dark:text-white leading-tight truncate">
                    {{ $classroom->course->name }}
                </h2>
            </div>
            <div class="text-right shrink-0">
                <a href="{{ route('student.edom.list') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-400 hover:text-red-500 transition-colors px-3 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                    BATAL
                </a>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="store" class="space-y-5 pb-20"> <!-- pb-20 agar konten terbawah tidak tertutup tombol sticky -->
        
        @foreach ($questions as $category => $list)
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <!-- Header Kategori (Lebih Kecil) -->
                <div class="bg-slate-50/80 dark:bg-slate-700/50 px-4 py-2.5 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="font-extrabold text-slate-700 dark:text-white uppercase tracking-wider text-xs flex items-center gap-2">
                        <span class="w-1 h-3 bg-brand-blue rounded-full"></span>
                        {{ $category }}
                    </h3>
                </div>

                <!-- List Pertanyaan (Compact Padding) -->
                <div class="divide-y divide-slate-100 dark:divide-slate-700/50">
                    @foreach ($list as $q)
                        <div class="p-4 hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                
                                <!-- Soal -->
                                <div class="flex-1">
                                    <div class="flex gap-2.5">
                                        <span class="text-xs font-bold text-slate-400 mt-0.5 select-none">{{ $loop->iteration }}.</span>
                                        <div>
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 leading-relaxed group-hover:text-slate-900 dark:group-hover:text-white transition-colors">
                                                {{ $q->question_text }}
                                            </p>
                                            @error("answers.{$q->id}")
                                                <p class="text-[10px] font-bold text-red-500 mt-1 animate-pulse flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    Wajib diisi
                                                </p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Opsi Rating (Compact & Responsif) -->
                                <div class="shrink-0 w-full md:w-auto bg-slate-50 md:bg-transparent dark:bg-slate-900/30 md:dark:bg-transparent p-2 md:p-0 rounded-lg">
                                    <div class="flex items-center justify-between md:justify-end gap-2 md:gap-1.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <label class="cursor-pointer relative group/radio">
                                                <input type="radio" wire:model="answers.{{ $q->id }}"
                                                    value="{{ $i }}" class="peer sr-only">
                                                
                                                <!-- Lingkaran Angka -->
                                                <div class="w-8 h-8 md:w-9 md:h-9 rounded-lg border border-slate-200 dark:border-slate-600 flex items-center justify-center text-xs font-bold text-slate-400 transition-all duration-200 
                                                            hover:border-brand-blue hover:text-brand-blue hover:bg-blue-50 dark:hover:bg-slate-700
                                                            peer-checked:border-brand-blue peer-checked:bg-brand-blue peer-checked:text-white peer-checked:shadow-md peer-checked:scale-110">
                                                    {{ $i }}
                                                </div>
                                                
                                                <!-- Label Hover (Sangat Baik/Buruk) -->
                                                @if($i == 1 || $i == 5)
                                                    <span class="absolute -bottom-4 left-1/2 -translate-x-1/2 text-[8px] font-bold text-slate-400 uppercase opacity-0 peer-checked:opacity-100 whitespace-nowrap transition-opacity">
                                                        {{ $i==1 ? 'Kurang' : 'Baik' }}
                                                    </span>
                                                @endif
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Footer Action (Floating Sticky) -->
        <div class="fixed bottom-6 left-0 right-0 z-30 flex justify-center pointer-events-none">
            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-lg p-1.5 rounded-2xl shadow-2xl border border-slate-200/50 dark:border-slate-700/50 pointer-events-auto transform transition-transform active:scale-95">
                <button type="submit"
                    class="flex items-center gap-2 px-8 py-3 bg-brand-blue text-white rounded-xl font-bold shadow-lg shadow-blue-900/30 hover:bg-blue-800 hover:shadow-blue-900/50 transition-all">
                    <span>Kirim Evaluasi</span>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </div>
        </div>

    </form>
</div>