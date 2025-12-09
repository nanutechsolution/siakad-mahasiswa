<div class="mx-auto max-w-5xl font-sans text-slate-600 dark:text-slate-300">
    
    <div class="mb-8">
        <h1 class="text-2xl font-black text-slate-900 dark:text-white">Biodata Mahasiswa</h1>
        <p class="text-slate-500 mt-1">Lengkapi data Anda untuk keperluan pelaporan PDDIKTI.</p>
    </div>

    <!-- Alert Sukses -->
    @if (session()->has('message'))
        <div x-data x-init="setTimeout(() => $el.remove(), 5000)" class="mb-6 flex items-center gap-3 rounded-xl bg-green-100 text-green-700 p-4 border border-green-200 shadow-sm animate-fade-in-up">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="font-bold">{{ session('message') }}</span>
        </div>
    @endif

    <form wire:submit.prevent="update">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- KOLOM KIRI: FOTO & DATA AKADEMIK (Sticky) -->
            <div class="space-y-6">
                
                <!-- Card Foto -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800 relative overflow-hidden group">
                    <div class="absolute top-0 left-0 w-full h-1 bg-brand-blue"></div>
                    
                    <div class="relative mx-auto mb-4 h-28 w-28">
                         <div class="h-full w-full rounded-full overflow-hidden ring-4 ring-slate-100 dark:ring-slate-700 relative shadow-lg">
                            <div wire:loading wire:target="photo" class="absolute inset-0 z-20 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm">
                                <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>

                            @if ($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="h-full w-full object-cover">
                            @elseif ($existing_photo)
                                <img src="{{ asset('storage/' . $existing_photo) }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-slate-200 to-slate-300 text-4xl font-bold text-slate-500 dark:from-slate-700 dark:to-slate-800 dark:text-slate-400">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <label for="photo-upload" class="absolute bottom-0 right-0 z-10 cursor-pointer rounded-full bg-brand-blue p-2 shadow-lg hover:bg-blue-600 transition-colors text-white ring-2 ring-white dark:ring-slate-800">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </label>
                        <input type="file" id="photo-upload" wire:model="photo" class="hidden" accept="image/*">
                    </div>

                    <h3 class="font-bold text-slate-900 dark:text-white text-lg leading-tight">{{ Auth::user()->name }}</h3>
                    <p class="text-sm text-slate-500 font-mono mt-1">{{ $student->nim }}</p>

                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 text-left space-y-3">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Program Studi</p>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $student->study_program->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Angkatan</p>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $student->entry_year }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Dosen Wali</p>
                            <p class="font-semibold text-slate-800 dark:text-white">{{ $student->academic_advisor->user->name ?? 'Belum Ditentukan' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Info Kontak -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-brand-blue dark:text-brand-gold">Kontak Utama</h4>
                    <div class="space-y-4">
                         <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Email (Login)</label>
                            <input wire:model="email" type="email" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">No. WhatsApp</label>
                            <input wire:model="phone" type="text" class="w-full rounded-lg border-slate-300 text-sm focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                            @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

            </div>

            <!-- KOLOM KANAN: FORM BIODATA LENGKAP -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- 1. DATA PRIBADI -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </span>
                        Identitas Diri (Sesuai KTP/KK)
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">NIK (Nomor Induk Kependudukan)</label>
                            <input wire:model="nik" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white" placeholder="16 Digit">
                            @error('nik') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">NISN (jika ada)</label>
                            <input wire:model="nisn" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tempat Lahir</label>
                            <input wire:model="pob" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tanggal Lahir</label>
                            <input wire:model="dob" type="date" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Agama</label>
                            <select wire:model="religion_id" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                <option value="">-- Pilih Agama --</option>
                                <option value="1">Islam</option>
                                <option value="2">Kristen Protestan</option>
                                <option value="3">Katolik</option>
                                <option value="4">Hindu</option>
                                <option value="5">Buddha</option>
                                <option value="6">Khonghucu</option>
                            </select>
                            @error('religion_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                         <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Jenis Kelamin</label>
                            <select wire:model="gender" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                         <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">NPWP (Opsional)</label>
                            <input wire:model="npwp" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                        </div>
                         <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kewarganegaraan</label>
                            <select wire:model="citizenship" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                                <option value="ID">Indonesia (WNI)</option>
                                <option value="WNA">Asing (WNA)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- 2. ALAMAT DOMISILI -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        </span>
                        Alamat Domisili
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Jalan / Nama Tempat</label>
                            <textarea wire:model="address" rows="2" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white" placeholder="Contoh: Jl. Soekarno Hatta No. 45"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">RT</label>
                                <input wire:model="rt" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white" placeholder="001">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">RW</label>
                                <input wire:model="rw" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white" placeholder="002">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Dusun / Lingkungan</label>
                                <input wire:model="dusun" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                            </div>
                        </div>

                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kelurahan / Desa</label>
                                <input wire:model="kelurahan" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Kode Pos</label>
                                <input wire:model="postal_code" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-900 dark:border-slate-600 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. DATA ORANG TUA -->
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                         <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </span>
                        Data Orang Tua / Wali
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Ayah -->
                        <div class="space-y-3 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700">
                            <h4 class="font-bold text-slate-500 uppercase text-xs tracking-wider border-b pb-2 mb-2 dark:border-slate-600">Data Ayah</h4>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">NIK Ayah</label>
                                <input wire:model="father_nik" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Nama Ayah</label>
                                <input wire:model="father_name" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                            </div>
                        </div>

                        <!-- Ibu -->
                        <div class="space-y-3 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-100 dark:border-slate-700">
                            <h4 class="font-bold text-slate-500 uppercase text-xs tracking-wider border-b pb-2 mb-2 dark:border-slate-600">Data Ibu Kandung</h4>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">NIK Ibu</label>
                                <input wire:model="mother_nik" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">Nama Ibu</label>
                                <input wire:model="mother_name" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                                @error('mother_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL SIMPAN (Floating) -->
                <div class="sticky bottom-6 flex justify-end z-30">
                    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md p-2 rounded-2xl shadow-xl border border-slate-200/50 dark:border-slate-700/50">
                        <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-brand-blue text-white rounded-xl font-bold shadow-lg shadow-blue-900/30 hover:bg-blue-800 hover:scale-105 transition-all transform">
                            <svg wire:loading.remove class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                            <svg wire:loading class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>