<div class="mx-auto max-w-4xl space-y-6">
    <x-slot name="header">Import Jadwal Kuliah</x-slot>

    <!-- Alert -->
    @if (session()->has('success'))
        <div class="p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-lg bg-red-100 text-red-700 font-bold border border-red-200">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- KOLOM KIRI: FORM UPLOAD -->
        <div class="md:col-span-2">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Upload File CSV</h3>
                
                @if(!$active_period)
                    <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg text-sm mb-4">
                        <strong>Perhatian:</strong> Belum ada Semester Aktif. Silakan aktifkan semester di menu Pengaturan terlebih dahulu.
                    </div>
                @else
                    <p class="text-sm text-slate-500 mb-4">
                        Jadwal akan diimport untuk Semester Aktif: <strong class="text-brand-blue">{{ $active_period->name }}</strong>
                    </p>

                    <form wire:submit.prevent="import" class="space-y-4">
                        <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 text-center hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                            <label class="block mt-2 text-sm font-medium text-slate-900 dark:text-white cursor-pointer">
                                <span>Pilih file CSV</span>
                                <input wire:model="file" type="file" accept=".csv" class="sr-only">
                            </label>
                            <p class="text-xs text-slate-500">Format .csv only (Max 2MB)</p>
                        </div>

                        @if ($file)
                            <div class="text-sm text-green-600 font-bold text-center">
                                File terpilih: {{ $file->getClientOriginalName() }}
                            </div>
                        @endif
                        @error('file') <span class="text-xs text-red-500 block text-center">{{ $message }}</span> @enderror

                        <div class="flex justify-end pt-4">
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2.5 bg-brand-blue text-white rounded-lg font-bold hover:bg-blue-800 transition shadow-lg flex items-center gap-2">
                                <span wire:loading.remove>Proses Import</span>
                                <span wire:loading>Memproses...</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <!-- LOG OUTPUT -->
            @if(!empty($import_logs))
                <div class="mt-6 bg-slate-900 text-slate-300 p-4 rounded-xl font-mono text-xs max-h-60 overflow-y-auto border border-slate-700">
                    <p class="text-white font-bold mb-2 border-b border-slate-700 pb-1">Log Proses:</p>
                    @foreach($import_logs as $log)
                        <div class="{{ str_contains($log, 'Gagal') ? 'text-red-400' : (str_contains($log, 'Warning') ? 'text-yellow-400' : 'text-green-400') }}">
                            > {{ $log }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- KOLOM KANAN: PANDUAN -->
        <div class="md:col-span-1">
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-100 dark:border-blue-800">
                <h4 class="font-bold text-blue-900 dark:text-blue-200 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Format File CSV
                </h4>
                <p class="text-xs text-blue-800 dark:text-blue-300 mb-4 leading-relaxed">
                    Pastikan file CSV Anda tidak memiliki header (baris pertama langsung data) atau abaikan baris pertama jika ada header. Urutan kolom wajib sbb:
                </p>
                
                <ol class="text-xs text-blue-800 dark:text-blue-300 list-decimal ml-4 space-y-1">
                    <li><strong>Kode Matkul</strong> (Wajib, cth: TI-101)</li>
                    <li><strong>Nama Kelas</strong> (Wajib, cth: A)</li>
                    <li><strong>NIDN Dosen</strong> (Opsional, cth: 00112233)</li>
                    <li><strong>Hari</strong> (cth: Senin)</li>
                    <li><strong>Jam Mulai</strong> (cth: 08:00)</li>
                    <li><strong>Jam Selesai</strong> (cth: 10:00)</li>
                    <li><strong>Ruangan</strong> (cth: R-302)</li>
                    <li><strong>Kuota</strong> (cth: 40)</li>
                </ol>

                <div class="mt-6 pt-4 border-t border-blue-200 dark:border-blue-800">
                     <a href="#" onclick="downloadTemplate(event)" class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Download Template Contoh
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>


<!-- Script Download Template -->
<script>
    function downloadTemplate(e) {
        e.preventDefault();
        
        // Header Kolom (Tanpa Spasi agar aman)
        const headers = ["KodeMatkul", "NamaKelas", "NIDNDosen", "Hari", "JamMulai", "JamSelesai", "Ruangan", "Kuota"];
        
        // Contoh Data (Dummy)
        const rows = [
            ["TI-101", "A", "00112233", "Senin", "08:00", "10:00", "R-101", "40"],
            ["SI-202", "B", "", "Selasa", "13:00", "15:00", "LAB-KOM", "30"]
        ];

        // Gabungkan Header dan Rows menjadi format CSV
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += headers.join(",") + "\r\n";
        
        rows.forEach(function(rowArray) {
            let row = rowArray.join(",");
            csvContent += row + "\r\n";
        });

        // Buat Element Link Virtual untuk Download
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "template_jadwal.csv");
        document.body.appendChild(link); // Required for FF

        link.click(); // Trigger download
        document.body.removeChild(link); // Bersihkan
    }
</script>