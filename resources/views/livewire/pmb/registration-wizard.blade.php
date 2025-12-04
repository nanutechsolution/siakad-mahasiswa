<div class="max-w-3xl mx-auto">
    @if (!$is_registration_open)
        <div class="bg-white rounded-2xl shadow-xl p-10 text-center border-t-4 border-red-500">
            <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-red-50 text-red-500 mb-6">
                <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 mb-2">Pendaftaran Belum Dibuka / Ditutup</h2>
            <p class="text-slate-500">
                Mohon maaf, saat ini tidak ada gelombang pendaftaran yang aktif.<br>
                Silakan pantau informasi jadwal pendaftaran di website resmi kampus.
            </p>
            <a href="/"
                class="mt-6 inline-block px-6 py-2 bg-slate-800 text-white rounded-lg font-bold hover:bg-slate-700">Kembali
                ke Beranda</a>
        </div>
    @else
        <!-- JIKA BUKA: TAMPILKAN INFO GELOMBANG -->
        <div class="mb-8 text-center">
            <span
                class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold uppercase mb-2 inline-block">
                {{ $active_wave->name }}
            </span>
            <h2 class="text-2xl font-bold text-slate-900">Formulir Pendaftaran Online</h2>
            <p class="text-slate-500">Lengkapi data berikut untuk mendaftar.</p>
        </div>


        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex justify-between mb-2">
                @foreach (['Data Diri', 'Sekolah & Ortu', 'Pilih Prodi', 'Berkas'] as $index => $label)
                    <span
                        class="text-xs font-bold uppercase {{ $currentStep > $index ? 'text-brand-blue' : 'text-slate-300' }}">
                        Step {{ $index + 1 }}
                    </span>
                @endforeach
            </div>
            <div class="h-2 bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full bg-brand-blue transition-all duration-500 ease-out"
                    style="width: {{ ($currentStep / 4) * 100 }}%"></div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
            <form wire:submit.prevent="submit">

                <!-- STEP 1: DATA DIRI -->
                @if ($currentStep == 1)
                    <div class="space-y-4 animate-fade-in">
                        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Data Pribadi</h3>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">NIK (Nomor Induk
                                Kependudukan)</label>
                            <input wire:model="nik" type="number" class="w-full rounded-lg border-slate-300">
                            @error('nik')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">NISN</label>
                            <input wire:model="nisn" type="number" class="w-full rounded-lg border-slate-300">
                            @error('nisn')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">No. WhatsApp Aktif</label>
                            <input wire:model="phone" type="number" class="w-full rounded-lg border-slate-300">
                            @error('phone')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                <!-- STEP 2: SEKOLAH & ORTU -->
                @if ($currentStep == 2)
                    <div class="space-y-4 animate-fade-in">
                        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Asal Sekolah & Orang Tua</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Sekolah</label>
                                <input wire:model="school_name" type="text"
                                    class="w-full rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Jurusan
                                    (IPA/IPS/Lain)</label>
                                <input wire:model="school_major" type="text"
                                    class="w-full rounded-lg border-slate-300">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Rata-rata Nilai Rapor (Semester
                                1-5)</label>
                            <input wire:model="average_grade" type="number" step="0.01"
                                class="w-full rounded-lg border-slate-300">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Ayah</label>
                                <input wire:model="father_name" type="text"
                                    class="w-full rounded-lg border-slate-300">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Nama Ibu</label>
                                <input wire:model="mother_name" type="text"
                                    class="w-full rounded-lg border-slate-300">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- STEP 3: PILIHAN PRODI -->
                @if ($currentStep == 3)
                    <div class="space-y-4 animate-fade-in">
                        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Pilihan Program Studi</h3>

                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl">
                            <label class="block text-sm font-bold text-blue-800 mb-2">Pilihan Pertama
                                (Prioritas)</label>
                            <select wire:model="first_choice_id" class="w-full rounded-lg border-blue-200">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach ($prodis as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->degree }})
                                    </option>
                                @endforeach
                            </select>
                            @error('first_choice_id')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pilihan Kedua (Opsional)</label>
                            <select wire:model="second_choice_id" class="w-full rounded-lg border-slate-300">
                                <option value="">-- Tidak Memilih --</option>
                                @foreach ($prodis as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->degree }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif

                <!-- STEP 4: UPLOAD BERKAS -->
                @if ($currentStep == 4)
                    <div class="space-y-6 animate-fade-in">
                        <h3 class="text-lg font-bold text-slate-800 border-b pb-2 mb-4">Upload Dokumen</h3>

                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Scan Ijazah / SKL
                                (PDF/JPG)</label>
                            <input wire:model="file_ijazah" type="file"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('file_ijazah')
                                <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Scan KTP / Kartu Pelajar
                                (PDF/JPG)</label>
                            <input wire:model="file_ktp" type="file"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @error('file_ktp')
                                <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                <!-- NAVIGATION BUTTONS -->
                <div class="flex justify-between mt-8 pt-6 border-t border-slate-100">
                    @if ($currentStep > 1)
                        <button type="button" wire:click="prevStep"
                            class="px-6 py-2 rounded-lg text-slate-500 hover:bg-slate-100 font-bold transition">
                            &larr; Kembali
                        </button>
                    @else
                        <div></div> <!-- Spacer -->
                    @endif

                    @if ($currentStep < $totalSteps)
                        <button type="button" wire:click="nextStep"
                            class="px-6 py-2 rounded-lg bg-slate-900 text-white font-bold hover:bg-slate-800 transition shadow-lg">
                            Lanjut &rarr;
                        </button>
                    @else
                        <button type="submit"
                            class="px-8 py-2 rounded-lg bg-green-600 text-white font-bold hover:bg-green-700 transition shadow-lg shadow-green-500/30">
                            Kirim Pendaftaran ✅
                        </button>
                    @endif
                </div>

            </form>
        </div>
    @endif
</div>
