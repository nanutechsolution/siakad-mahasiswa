<div class="space-y-6 font-sans">
    <x-slot name="header">Computer Based Test (CBT)</x-slot>

    <!-- 1. SUB-NAVBAR (MENU MODUL UJIAN) -->
    <div
        class="flex flex-col md:flex-row gap-4 md:items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-4">
        <nav class="flex space-x-2 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
            <button
                class="px-4 py-2 text-sm font-bold rounded-lg bg-white dark:bg-slate-700 text-brand-blue dark:text-white shadow-sm ring-1 ring-black/5">
                Bank Soal
            </button>
            <button
                class="px-4 py-2 text-sm font-medium rounded-lg text-slate-500 hover:text-slate-700 hover:bg-white/50 dark:text-slate-400 dark:hover:text-slate-200 transition-all">
                Jadwal Ujian
            </button>
            <button
                class="px-4 py-2 text-sm font-medium rounded-lg text-slate-500 hover:text-slate-700 hover:bg-white/50 dark:text-slate-400 dark:hover:text-slate-200 transition-all">
                Hasil Seleksi
            </button>
        </nav>

        <div class="flex items-center gap-4 text-xs font-bold text-slate-500 dark:text-slate-400">
            <div class="flex items-center gap-1">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                {{ $questions->total() }} Soal Aktif
            </div>
        </div>
    </div>

    <!-- 2. ACTION BAR (SEARCH & ADD) -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="w-full md:w-96 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text"
                class="block w-full pl-10 rounded-xl border-slate-300 bg-white text-slate-900 focus:ring-brand-blue focus:border-brand-blue dark:bg-slate-800 dark:border-slate-700 dark:text-white"
                placeholder="Cari pertanyaan...">
        </div>

        <button wire:click="create"
            class="flex items-center gap-2 px-5 py-2.5 bg-brand-blue text-white rounded-xl font-bold shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition-all transform hover:-translate-y-0.5">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Buat Soal Baru
        </button>
    </div>

    <!-- Alert -->
    @if (session()->has('message'))
        <div
            class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-3 animate-fade-in-up">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <!-- 3. QUESTION LIST -->
    <div class="grid grid-cols-1 gap-4">
        @forelse($questions as $q)
            <div
                class="group bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:border-brand-blue dark:hover:border-brand-blue transition-all">
                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center font-bold text-slate-500 dark:text-slate-300">
                        {{ $loop->iteration + ($questions->currentPage() - 1) * $questions->perPage() }}
                    </div>

                    <div class="flex-1">
                        <p class="text-lg font-bold text-slate-800 dark:text-white mb-4 leading-relaxed">
                            {{ $q->question_text }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach (['A', 'B', 'C', 'D'] as $opt)
                                @php
                                    $isCorrect = $q->correct_answer == $opt;
                                    $optionText = $q->{'option_' . strtolower($opt)};
                                @endphp
                                <div
                                    class="flex items-center gap-3 p-3 rounded-xl border {{ $isCorrect ? 'bg-green-50 border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-slate-50 border-transparent dark:bg-slate-700/30' }}">
                                    <div
                                        class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $isCorrect ? 'bg-green-600 text-white' : 'bg-slate-200 text-slate-500 dark:bg-slate-600 dark:text-slate-300' }}">
                                        {{ $opt }}
                                    </div>
                                    <span
                                        class="text-sm font-medium {{ $isCorrect ? 'text-green-800 dark:text-green-300' : 'text-slate-600 dark:text-slate-400' }}">
                                        {{ $optionText }}
                                    </span>
                                    @if ($isCorrect)
                                        <svg class="w-4 h-4 text-green-600 ml-auto" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div
                        class="flex flex-col gap-2 opacity-100 lg:opacity-0 group-hover:opacity-100 transition-opacity">
                        <button wire:click="edit({{ $q->id }})"
                            class="p-2 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300"
                            title="Edit">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button wire:click="delete({{ $q->id }})" wire:confirm="Yakin hapus soal ini?"
                            class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-300"
                            title="Hapus">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="flex flex-col items-center justify-center py-16 text-center bg-white dark:bg-slate-800 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                <div class="p-4 rounded-full bg-slate-50 dark:bg-slate-700 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">Bank Soal Kosong</h3>
                <p class="text-slate-500 mt-1">Belum ada pertanyaan yang ditambahkan untuk ujian.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $questions->links() }}
    </div>

    <!-- 4. MODAL FORM INPUT -->
    @if ($isModalOpen)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
            <div
                class="w-full max-w-2xl bg-white dark:bg-slate-800 rounded-2xl shadow-2xl my-8 transform transition-all">

                <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                        {{ $isEditMode ? 'Edit Pertanyaan' : 'Buat Pertanyaan Baru' }}
                    </h3>
                    <button wire:click="$set('isModalOpen', false)"
                        class="text-slate-400 hover:text-red-500 text-2xl">&times;</button>
                </div>

                <form wire:submit.prevent="store" class="p-6 space-y-6">

                    <!-- Soal -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Teks
                            Pertanyaan</label>
                        <textarea wire:model="question_text" rows="3"
                            class="w-full rounded-xl border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white focus:ring-brand-blue"
                            placeholder="Contoh: Siapakah penemu bahasa pemrograman PHP?"></textarea>
                        @error('question_text')
                            <span class="text-xs text-red-500 font-bold block mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Pilihan Ganda -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach (['a', 'b', 'c', 'd'] as $opt)
                            <div wire:key="opt-field-{{ $opt }}">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Opsi
                                    {{ strtoupper($opt) }}</label>
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-300 text-lg">{{ strtoupper($opt) }}</span>

                                    <input wire:model="options.{{ $opt }}" type="text"
                                        class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white focus:ring-brand-blue">

                                </div>
                                @error("options.{$opt}")
                                    <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <!-- Kunci Jawaban -->
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Kunci Jawaban
                            Benar</label>
                        <div class="flex gap-4">
                            @foreach (['A', 'B', 'C', 'D'] as $key)
                                <label wire:key="key-select-{{ $key }}"
                                    class="cursor-pointer relative group">
                                    <input type="radio" wire:model="correct_answer" value="{{ $key }}"
                                        class="peer sr-only">
                                    <div
                                        class="w-14 h-14 rounded-2xl border-2 border-slate-200 dark:border-slate-600 flex items-center justify-center font-black text-xl text-slate-400 transition-all peer-checked:bg-green-500 peer-checked:text-white peer-checked:border-green-500 peer-checked:shadow-lg peer-checked:scale-110 group-hover:border-green-200">
                                        {{ $key }}
                                    </div>
                                    <div
                                        class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-[10px] font-bold text-green-600 opacity-0 peer-checked:opacity-100 transition-opacity">
                                        BENAR
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('correct_answer')
                            <span class="text-xs text-red-500 font-bold block mt-2">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-6 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" wire:click="$set('isModalOpen', false)"
                            class="px-6 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700 font-bold transition">
                            Batal
                        </button>
                        <!-- TOMBOL SIMPAN DENGAN LOADING STATE -->
                        <button type="submit" wire:loading.attr="disabled"
                            class="px-8 py-2.5 rounded-xl bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-lg shadow-blue-900/30 transition transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <span wire:loading.remove>Simpan Soal</span>
                            <span wire:loading>Menyimpan...</span>
                            <svg wire:loading class="animate-spin h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif

</div>
