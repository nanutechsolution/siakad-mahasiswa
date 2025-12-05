<div>
    <x-slot name="header">Pengaturan Format NIM</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: FORM -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-6">Konfigurasi Format</h3>
                
                @if (session()->has('message'))
                    <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-6">
                    
                    <!-- 1. Format Tahun -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Format Tahun Angkatan</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="year_format" value="YY" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 peer-checked:border-brand-blue peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all text-center">
                                    <span class="block font-bold text-slate-800 dark:text-white">2 Digit</span>
                                    <span class="text-xs text-slate-500">Contoh: 25</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="year_format" value="YYYY" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 peer-checked:border-brand-blue peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all text-center">
                                    <span class="block font-bold text-slate-800 dark:text-white">4 Digit</span>
                                    <span class="text-xs text-slate-500">Contoh: 2025</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Format Prodi -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Identitas Prodi</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="prodi_source" value="CODE" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 peer-checked:border-brand-blue peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all text-center">
                                    <span class="block font-bold text-slate-800 dark:text-white">Kode Huruf</span>
                                    <span class="text-xs text-slate-500">Contoh: TI, SI, MN</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="prodi_source" value="ID" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 peer-checked:border-brand-blue peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all text-center">
                                    <span class="block font-bold text-slate-800 dark:text-white">Kode Angka (ID)</span>
                                    <span class="text-xs text-slate-500">Contoh: 01, 02, 05</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 3. Panjang Digit -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Jumlah Digit Urut</label>
                        <input type="range" wire:model.live="seq_digit" min="3" max="5" step="1" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer dark:bg-slate-700">
                        <div class="flex justify-between text-xs text-slate-500 mt-2">
                            <span>3 Digit (001)</span>
                            <span>4 Digit (0001)</span>
                            <span>5 Digit (00001)</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" class="px-6 py-3 bg-brand-blue text-white font-bold rounded-lg hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 w-full md:w-auto">
                            Simpan Pengaturan
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: PREVIEW -->
        <div>
            <div class="sticky top-6 space-y-6">
                <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-brand-gold/20 rounded-full blur-2xl"></div>
                    
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Preview Hasil</h4>
                    
                    <div class="text-center py-6">
                        <p class="text-sm text-slate-400 mb-1">Mahasiswa ke-1 Teknik Informatika Tahun {{ date('Y') }}</p>
                        <p class="text-4xl font-black tracking-widest font-mono text-brand-gold animate-pulse">
                            {{ $preview_nim }}
                        </p>
                    </div>

                    <div class="mt-4 pt-4 border-t border-white/10 text-xs text-slate-400 space-y-1">
                        <p>• Tahun: <span class="text-white">{{ $year_format == 'YYYY' ? '4 Digit' : '2 Digit' }}</span></p>
                        <p>• Prodi: <span class="text-white">{{ $prodi_source == 'CODE' ? 'Kode Huruf' : 'ID Angka' }}</span></p>
                        <p>• Digit: <span class="text-white">{{ $seq_digit }} Angka</span></p>
                    </div>
                </div>

                <div class="bg-yellow-50 text-yellow-800 p-4 rounded-xl border border-yellow-200 text-sm">
                    <strong>Catatan:</strong><br>
                    Perubahan ini hanya akan mempengaruhi NIM mahasiswa baru yang digenerate setelah pengaturan ini disimpan. NIM lama tidak akan berubah.
                </div>
            </div>
        </div>

    </div>
</div>