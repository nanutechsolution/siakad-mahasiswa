<div>
    <x-slot name="header">Generate KRS Paket</x-slot>

    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200 shadow-sm">
            ✅ {{ session('message') }}
        </div>
    @endif
    @if (session()->has('warning'))
        <div class="mb-6 p-4 rounded-lg bg-yellow-100 text-yellow-800 font-bold border border-yellow-200 shadow-sm">
            ⚠️ {{ session('warning') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 font-bold border border-red-200 shadow-sm">
            ❌ {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2">
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                <h3
                    class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b pb-2 border-slate-100 dark:border-slate-700">
                    Parameter Generate
                </h3>

                <form wire:submit.prevent="generate" class="space-y-5">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Program
                            Studi</label>
                        <select wire:model.live="prodi_id"
                            class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">-- Pilih Prodi --</option>
                            @foreach ($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->degree }})</option>
                            @endforeach
                        </select>
                        @error('prodi_id')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Angkatan
                                Mahasiswa</label>
                            <input wire:model.live="entry_year" type="number"
                                class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                placeholder="2024">
                            @error('entry_year')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Paket
                                Semester</label>
                            <select wire:model.live="target_semester"
                                class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                <option value="1">Semester 1</option>
                                <option value="2">Semester 2</option>
                                <option value="3">Semester 3</option>
                                <option value="4">Semester 4</option>
                                <option value="5">Semester 5</option>
                                <option value="6">Semester 6</option>
                                <option value="7">Semester 7</option>
                                <option value="8">Semester 8</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Masukkan ke
                            Kelas?</label>
                        <div class="flex items-center gap-4">
                            <input wire:model="class_name" type="text"
                                class="w-24 text-center font-bold uppercase rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                placeholder="A">
                            <span class="text-xs text-slate-500">
                                Sistem akan mencari kelas dengan nama ini untuk setiap matkul.<br>
                                Pastikan Anda sudah membuka kelas ini di menu Penjadwalan.
                            </span>
                        </div>
                        @error('class_name')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            wire:confirm="Yakin ingin melakukan generate massal? Proses ini akan langsung menyetujui (ACC) KRS mahasiswa."
                            class="w-full bg-brand-blue hover:bg-blue-800 text-white font-bold py-3 rounded-lg shadow-lg shadow-blue-500/30 transition-all">
                            ⚡ EKSEKUSI GENERATE
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="bg-indigo-50 dark:bg-slate-800 p-6 rounded-xl border border-indigo-100 dark:border-slate-700">
                <h4 class="font-bold text-indigo-900 dark:text-indigo-400 mb-4 uppercase tracking-wider text-xs">
                    Estimasi Hasil</h4>

                <div class="space-y-4">
                    <div class="flex justify-between items-center bg-white dark:bg-slate-700 p-4 rounded-lg shadow-sm">
                        <span class="text-sm text-slate-600 dark:text-slate-300">Target Mahasiswa</span>
                        <span
                            class="text-2xl font-black text-slate-800 dark:text-white">{{ $preview_students_count }}</span>
                    </div>

                    <div class="flex justify-between items-center bg-white dark:bg-slate-700 p-4 rounded-lg shadow-sm">
                        <span class="text-sm text-slate-600 dark:text-slate-300">Jumlah Matkul</span>
                        <span
                            class="text-2xl font-black text-slate-800 dark:text-white">{{ $preview_courses_count }}</span>
                    </div>

                    <div class="border-t border-indigo-200 dark:border-slate-600 my-4"></div>

                    <div class="text-center">
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">Total Record KRS yang akan dibuat:
                        </p>
                        <p class="text-4xl font-black text-brand-blue dark:text-brand-gold">
                            {{ $preview_students_count * $preview_courses_count }}
                        </p>
                    </div>
                </div>

                <div
                    class="mt-6 text-xs text-indigo-800 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900/30 p-3 rounded border border-indigo-200 dark:border-indigo-800">
                    <strong>Catatan:</strong><br>
                    Fitur ini akan melewati validasi bentrok jadwal dan kuota. Gunakan hanya untuk mahasiswa baru (Maba)
                    atau paket kurikulum tetap.
                </div>
            </div>
        </div>

    </div>
</div>
