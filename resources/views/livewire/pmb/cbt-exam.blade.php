<div class="min-h-screen bg-slate-50 p-6 font-sans">
    
    <!-- HEADER TIMER (Sticky) -->
    <div class="fixed top-0 left-0 right-0 bg-white shadow-md z-50 px-6 py-4 flex justify-between items-center" 
         x-data="{ time: {{ $time_remaining }} }" 
         x-init="setInterval(() => { if(time > 0) time--; else $wire.finishExam() }, 1000)">
        
        <div>
            <h1 class="font-bold text-lg text-slate-800">Ujian Seleksi PMB</h1>
            <p class="text-xs text-slate-500">{{ Auth::user()->name }} - {{ $registrant->registration_no }}</p>
        </div>
        
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold uppercase text-slate-400">Sisa Waktu</span>
            <div class="bg-red-100 text-red-600 font-mono font-bold text-xl px-4 py-1 rounded-lg">
                <span x-text="Math.floor(time / 60).toString().padStart(2, '0')"></span>:<span x-text="(time % 60).toString().padStart(2, '0')"></span>
            </div>
        </div>
    </div>

    <!-- SOAL AREA -->
    <div class="max-w-3xl mx-auto mt-24 pb-20">
        <form wire:submit.prevent="finishExam">
            @foreach($questions as $index => $q)
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 mb-6">
                    <div class="flex gap-4">
                        <span class="flex-shrink-0 w-8 h-8 bg-slate-100 rounded-full flex items-center justify-center font-bold text-slate-600">{{ $index + 1 }}</span>
                        <div class="flex-1">
                            <p class="text-lg font-medium text-slate-800 mb-4">{{ $q->question_text }}</p>
                            
                            <div class="space-y-3">
                                @foreach(['A', 'B', 'C', 'D'] as $opt)
                                    <label class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:bg-slate-50 cursor-pointer transition-colors {{ ($answers[$q->id] ?? '') == $opt ? 'bg-blue-50 border-blue-200 ring-1 ring-blue-200' : '' }}">
                                        <input type="radio" wire:model.live="answers.{{ $q->id }}" value="{{ $opt }}" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                        <span class="text-sm text-slate-700">
                                            <span class="font-bold text-slate-400 mr-2">{{ $opt }}.</span> 
                                            {{ $q->{'option_'.strtolower($opt)} }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 flex justify-center z-40">
                <button type="submit" 
                        wire:confirm="Yakin ingin mengakhiri ujian? Jawaban akan dikunci."
                        class="px-8 py-3 bg-green-600 text-white font-bold rounded-xl shadow-lg hover:bg-green-700 transition-transform hover:scale-105">
                    Selesai & Kumpulkan Jawaban
                </button>
            </div>
        </form>
    </div>
</div>