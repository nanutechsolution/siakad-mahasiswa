<div class="mx-auto max-w-6xl space-y-8">
<x-slot name="header">Kurikulum & Import Matakuliah</x-slot>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- FORM UPLOAD -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-8">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">Upload File Kurikulum</h3>
            
            @if (session()->has('message'))
                <div class="mb-4 p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit.prevent="import" class="space-y-6">
                <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl p-10 text-center hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors group">
                    <input wire:model="file" type="file" accept=".csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="space-y-2">
                        <div class="mx-auto w-16 h-16 bg-blue-50 dark:bg-blue-900/30 rounded-full flex items-center justify-center text-brand-blue group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-700 dark:text-slate-200">
                            @if($file) {{ $file->getClientOriginalName() }} @else Klik untuk pilih file CSV @endif
                        </p>
                        <p class="text-xs text-slate-400">Format .csv (Max 2MB)</p>
                    </div>
                </div>
                @error('file') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror

                <div class="flex justify-end">
                    <button type="submit" wire:loading.attr="disabled" class="px-8 py-3 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 flex items-center gap-2">
                        <span wire:loading.remove>Mulai Import</span>
                        <span wire:loading class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                        <span wire:loading>Memproses...</span>
                    </button>
                </div>
            </form>
        </div>

        @if($show_results && count($import_logs) > 0)
        <div class="bg-slate-900 rounded-2xl p-6 text-slate-300 font-mono text-xs overflow-hidden">
            <h4 class="text-white font-bold mb-4">Log Proses Import</h4>
            <div class="max-h-60 overflow-y-auto custom-scrollbar">
                @foreach($import_logs as $log)
                    <div class="py-0.5 border-b border-white/5 opacity-80">> {{ $log }}</div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- PANDUAN KOLOM -->
    <div class="space-y-6">
        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl p-6 border border-indigo-100 dark:border-indigo-800">
            <h4 class="font-bold text-indigo-900 dark:text-indigo-200 mb-4 flex items-center gap-2 text-sm uppercase">Aturan Kolom CSV</h4>
            <ul class="space-y-3 text-xs">
                <li><b class="text-indigo-900">1. Kode Prodi</b> (Misal: TI)</li>
                <li><b class="text-indigo-900">2. Kode Matkul</b> (Misal: TI201)</li>
                <li><b class="text-indigo-900">3. Nama Matkul</b></li>
                <li><b class="text-indigo-900">4. SKS</b> (Angka)</li>
                <li><b class="text-indigo-900">5. Semester</b> (1-8)</li>
                <li><b class="text-indigo-900">6. Wajib?</b> (Y/T)</li>
                <li>
                    <b class="text-indigo-900">7. Prasyarat</b><br>
                    Format: <code class="bg-indigo-100 dark:bg-indigo-900/50 px-1 rounded">KODE:NILAI</code> dipisah titik koma.<br>
                    Contoh: <code class="bg-indigo-100 dark:bg-indigo-900/50 px-1 rounded">TI101:B;TI105:C</code>
                </li>
            </ul>

            <div class="mt-8 pt-4 border-t border-indigo-100 dark:border-indigo-800">
                <button onclick="downloadCourseTemplate()" class="flex items-center gap-2 text-xs font-bold text-brand-blue hover:underline">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Unduh Template Terbaru
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MONITORING DATA -->
<div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="p-8 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Review Kurikulum</h3>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <select wire:model.live="filter_prodi" class="rounded-xl border-slate-300 text-sm dark:bg-slate-900 dark:border-slate-700">
                <option value="">Semua Prodi</option>
                @foreach($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Kode/Nama..." class="rounded-xl border-slate-300 text-sm dark:bg-slate-900 dark:border-slate-700 w-full md:w-48">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 font-bold uppercase text-[10px] tracking-widest border-b border-slate-100 dark:border-slate-700">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Nama Matakuliah</th>
                    <th class="px-6 py-4 text-center">SKS</th>
                    <th class="px-6 py-4">Prasyarat & Min. Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($courses as $course)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-6 py-4 font-mono font-bold text-brand-blue">{{ $course->code }}</td>
                    <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">{{ $course->name }}</td>
                    <td class="px-6 py-4 text-center font-bold">{{ $course->credit_total }}</td>
                    <td class="px-6 py-4">
                        @if($course->prerequisites->count() > 0)
                            <div class="flex flex-wrap gap-1">
                                @foreach($course->prerequisites as $pre)
                                    <span class="bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 text-[10px] font-bold px-1.5 py-0.5 rounded border border-orange-100 dark:border-orange-800">
                                        {{ $pre->code }} ({{ $pre->pivot->min_grade }})
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-slate-300 text-xs">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Data kosong.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700">
        {{ $courses->links() }}
    </div>
</div>


</div>

<script>
/**
* Fungsi untuk menghasilkan dan mengunduh template CSV Kurikulum
*/
function downloadCourseTemplate() {
const headers = ["KodeProdi", "KodeMatkul", "NamaMatkul", "SKS", "Semester", "Wajib_Y_T", "Prasyarat_KodeGrade"];
const rows = [
["TI", "TI101", "Algoritma Pemrograman", "3", "1", "Y", ""],
["TI", "TI201", "Struktur Data", "3", "2", "Y", "TI101:B"],
["TI", "TI305", "Web Lanjut", "3", "3", "T", "TI101:C;TI201:B"]
];

    // Membangun konten CSV
    let csvContent = headers.join(&quot;,&quot;) + &quot;\r\n&quot;;
    
    rows.forEach(function(rowArray) {
        let row = rowArray.join(&quot;,&quot;);
        csvContent += row + &quot;\r\n&quot;;
    });

    // Membuat blob data untuk pengunduhan yang lebih aman
    const blob = new Blob([csvContent], { type: &#39;text/csv;charset=utf-8;&#39; });
    const url = URL.createObjectURL(blob);
    
    // Proses pengunduhan menggunakan elemen jangkar (anchor) virtual
    const link = document.createElement(&quot;a&quot;);
    link.setAttribute(&quot;href&quot;, url);
    link.setAttribute(&quot;download&quot;, &quot;template_import_kurikulum.csv&quot;);
    link.style.visibility = &#39;hidden&#39;;
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Bersihkan memori URL
    URL.revokeObjectURL(url);
}


</script>