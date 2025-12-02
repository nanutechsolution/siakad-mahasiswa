<div class="mx-auto max-w-5xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Pengaturan Profil</h1>
        <p class="text-slate-500">Kelola informasi pribadi dan keamanan akun Anda.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 flex items-center gap-3 rounded-lg bg-green-50 p-4 text-green-700 border border-green-200">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-bold">{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <div class="space-y-6">

            <div
                class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="relative mx-auto mb-4 h-24 w-24">

                    <div
                        class="h-full w-full rounded-full overflow-hidden ring-4 ring-slate-100 dark:ring-slate-700 relative">

                        <div wire:loading wire:target="photo"
                            class="absolute inset-0 z-20 flex items-center justify-center bg-slate-900/50">
                            <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>

                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" class="h-full w-full object-cover">
                        @elseif ($existing_photo)
                            <img src="{{ asset('storage/' . $existing_photo) }}" class="h-full w-full object-cover">
                        @else
                            <div
                                class="flex h-full w-full items-center justify-center bg-brand-blue text-3xl font-bold text-white">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>

                    <label for="photo-upload"
                        class="absolute bottom-0 right-0 z-10 cursor-pointer rounded-full bg-white p-2 shadow-lg border border-slate-200 hover:bg-slate-50 transition-colors dark:bg-slate-700 dark:border-slate-600 dark:hover:bg-slate-600">
                        <svg class="h-4 w-4 text-slate-600 dark:text-slate-300" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </label>

                    <input type="file" id="photo-upload" wire:model="photo" class="hidden" accept="image/*">
                </div>

                <h3 class="font-bold text-slate-900 dark:text-white">{{ Auth::user()->name }}</h3>
                <p class="text-sm text-slate-500">{{ $student->nim }}</p>

                <div class="mt-4 flex justify-center">
                    <span
                        class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        {{ $student->study_program->name ?? 'Prodi -' }}
                    </span>
                </div>
            </div>

            <div
                class="rounded-xl border border-slate-200 bg-slate-50 p-6 shadow-inner dark:border-slate-700 dark:bg-slate-800/50">
                <h4 class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-500">Data Akademik</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Angkatan</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $student->entry_year }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="font-bold text-green-600">Aktif</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Dosen Wali</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">-</span>
                    </div>
                </div>
                <p class="mt-4 text-[10px] text-slate-400 italic text-center">
                    *Data akademik hanya bisa diubah oleh BAAK.
                </p>
            </div>

        </div>

        <div class="md:col-span-2">
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-100 px-6 py-4 dark:border-slate-700">
                    <h3 class="font-bold text-slate-900 dark:text-white">Biodata Diri</h3>
                </div>

                <form wire:submit.prevent="update" class="p-6 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Email
                                Akun</label>
                            <input wire:model="email" type="email"
                                class="w-full rounded-lg border-slate-300 focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            @error('email')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Nomor
                                WhatsApp</label>
                            <input wire:model="phone" type="text"
                                class="w-full rounded-lg border-slate-300 focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            @error('phone')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Tempat
                                Lahir</label>
                            <input wire:model="pob" type="text"
                                class="w-full rounded-lg border-slate-300 focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Tanggal
                                Lahir</label>
                            <input wire:model="dob" type="date"
                                class="w-full rounded-lg border-slate-300 focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-300">Alamat
                            Domisili</label>
                        <textarea wire:model="address" rows="3"
                            class="w-full rounded-lg border-slate-300 focus:border-brand-blue focus:ring-brand-blue dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                            placeholder="Nama Jalan, RT/RW, Kelurahan, Kecamatan..."></textarea>
                        @error('address')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit"
                            class="flex items-center gap-2 rounded-lg bg-brand-blue px-6 py-2.5 text-sm font-bold text-white hover:bg-blue-800 transition-colors shadow-lg shadow-blue-500/30">
                            <span wire:loading.remove>Simpan Perubahan</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
