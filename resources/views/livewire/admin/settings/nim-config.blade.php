<div>
    <x-slot name="header">Pengaturan Format NIM</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: KONFIGURASI GLOBAL -->
        <div class="lg:col-span-1 space-y-6">
            
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 sticky top-6">
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-6">Pola Dasar</h3>
                
                @if (session()->has('message'))
                    <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200 text-sm">
                        ✅ {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="save" class="space-y-6">
                    
                    <!-- 1. Format Tahun -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tahun Angkatan</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="year_format" value="YY" class="peer sr-only">
                                <div class="p-3 rounded-lg border-2 border-slate-200 dark:border-slate-600 peer-checked:border-brand-blue peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all text-center">
                                    <span class="block font-bold text-slate-800 dark:text-white text-sm">2 Digit</span>
                                    <span class="text-[10px] text-slate-500">25</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" wire:model.live="year_format" value="YYYY" class="peer sr-only">
                                <div class="p-3 rounded-lg border-2 border-slate-200 dark:border-slate-600 peer-checked:border-brand-blue peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 transition-all text-center">
                                    <span class="block font-bold text-slate-800 dark:text-white text-sm">4 Digit</span>
                                    <span class="text-[10px] text-slate-500">2025</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- 2. Digit Urut -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Jumlah Digit Urut</label>
                        <div class="flex items-center gap-4">
                            <input type="range" wire:model.live="seq_digit" min="3" max="6" step="1" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer dark:bg-slate-700">
                            <span class="font-mono font-bold text-brand-blue text-lg">{{ $seq_digit }}</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Contoh: {{ str_pad('1', $seq_digit, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" class="w-full px-6 py-3 bg-brand-blue text-white font-bold rounded-lg hover:bg-blue-800 transition shadow-lg shadow-blue-900/20">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- KOLOM KANAN: MAPPING KODE PRODI -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                    <h3 class="font-bold text-slate-800 dark:text-white">Mapping Kode Prodi</h3>
                    <p class="text-sm text-slate-500">Masukkan kode angka/huruf unik untuk setiap Program Studi. Kode ini akan disisipkan di tengah NIM.</p>
                </div>

                <div class="p-0">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-100 dark:bg-slate-700 text-slate-500 font-bold text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3">Program Studi</th>
                                <th class="px-6 py-3 w-40">Kode NIM</th>
                                <th class="px-6 py-3">Preview Hasil</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($all_prodis as $index => $prodi)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800 dark:text-white">{{ $prodi->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $prodi->degree }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <!-- Input Kode Per Prodi -->
                                    <input type="text" 
                                           wire:model.live.debounce.500ms="prodi_codes.{{ $prodi->id }}"
                                           class="w-full text-center font-mono font-bold text-brand-blue rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white focus:ring-brand-blue focus:border-brand-blue"
                                           placeholder="{{ $prodi->code }}">
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        // Cari preview yang cocok dari array simulations
                                        $sim = collect($simulations)->firstWhere('name', $prodi->name);
                                    @endphp
                                    <span class="font-mono text-lg font-bold text-slate-700 dark:text-slate-300 tracking-wide">
                                        {{ $sim['example'] ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 p-4 bg-blue-50 text-blue-800 rounded-xl border border-blue-200 text-sm flex gap-3 items-start dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800">
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div>
                    <strong>Tips:</strong>
                    <ul class="list-disc ml-4 mt-1 space-y-1">
                        <li>Anda bisa menggunakan <strong>Angka</strong> (misal: 55, 57) atau <strong>Huruf</strong> (misal: TI, SI).</li>
                        <li>Pastikan kode unik antar prodi agar NIM tidak bentrok.</li>
                        <li>Perubahan di sini langsung berpengaruh pada NIM mahasiswa baru yang akan digenerate via PMB.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>