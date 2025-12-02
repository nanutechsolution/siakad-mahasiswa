<div>
    <x-slot name="header">Pengaturan Sistem</x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <div class="space-y-6">
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6">
                <h3 class="text-lg font-bold text-brand-blue dark:text-brand-gold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Identitas Kampus
                </h3>

                @if (session()->has('message_identity'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                        {{ session('message_identity') }}
                    </div>
                @endif

                <form wire:submit.prevent="saveIdentity" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Logo
                            Kampus</label>
                        <div class="flex items-center gap-4">
                            @if ($logo)
                                <img src="{{ $logo->temporaryUrl() }}"
                                    class="w-16 h-16 object-contain border rounded p-1">
                            @elseif($old_logo)
                                <img src="{{ asset('storage/' . $old_logo) }}"
                                    class="w-16 h-16 object-contain border rounded p-1">
                            @else
                                <div
                                    class="w-16 h-16 bg-slate-100 rounded flex items-center justify-center text-slate-400">
                                    No Logo</div>
                            @endif
                            <input type="file" wire:model="logo"
                                class="text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-brand-blue file:text-white hover:file:bg-blue-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama Kampus</label>
                        <input type="text" wire:model="campus_name"
                            class="w-full mt-1 rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Email</label>
                            <input type="email" wire:model="campus_email"
                                class="w-full mt-1 rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">No.
                                Telepon</label>
                            <input type="text" wire:model="campus_phone"
                                class="w-full mt-1 rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Alamat
                            Lengkap</label>
                        <textarea wire:model="campus_address" rows="3"
                            class="w-full mt-1 rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white shadow-sm focus:border-brand-gold focus:ring-brand-gold"></textarea>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4 mt-4">
                        <h4 class="text-xs font-bold uppercase text-brand-blue mb-3 dark:text-brand-gold">Pejabat Kampus
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama
                                    Yayasan</label>
                                <input type="text" wire:model="foundation_name" placeholder="Yayasan Stella Maris..."
                                    class="w-full mt-1 rounded-md border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Ketua
                                    Yayasan</label>
                                <input type="text" wire:model="foundation_head" placeholder="Nama Ketua..."
                                    class="w-full mt-1 rounded-md border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama
                                    Rektor</label>
                                <input type="text" wire:model="rector_name" placeholder="Dr. ..."
                                    class="w-full mt-1 rounded-md border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">NIP
                                    Rektor</label>
                                <input type="text" wire:model="rector_nip"
                                    class="w-full mt-1 rounded-md border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4 mt-4">
                        <h4 class="text-xs font-bold uppercase text-brand-blue mb-3 dark:text-brand-gold">Rekening
                            Pembayaran</h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nama
                                    Bank</label>
                                <input type="text" wire:model="bank_name" placeholder="Contoh: Bank BRI"
                                    class="w-full mt-1 rounded-md border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Nomor
                                    Rekening</label>
                                <input type="text" wire:model="bank_account" placeholder="Contoh: 1234-5678"
                                    class="w-full mt-1 rounded-md border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white font-mono">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Atas
                                    Nama</label>
                                <input type="text" wire:model="bank_holder" placeholder="Contoh: Yayasan..."
                                    class="w-full mt-1 rounded-md border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                            </div>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="px-4 py-2 bg-brand-blue text-white rounded hover:bg-blue-900 transition-colors w-full md:w-auto">
                            Simpan Identitas & Bank
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-brand-gold/10 rounded-bl-full -mr-4 -mt-4"></div>

                <h3 class="text-lg font-bold text-brand-blue dark:text-brand-gold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Kontrol Akademik
                </h3>

                @if (session()->has('message_academic'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 rounded text-sm">
                        {{ session('message_academic') }}
                    </div>
                @endif

                <form wire:submit.prevent="saveAcademic" class="space-y-6">

                    <div
                        class="p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                        <label class="block text-sm font-bold text-slate-800 dark:text-white mb-2">Periode Akademik
                            Aktif</label>
                        <select wire:model="active_period_id"
                            class="w-full rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-white shadow-sm focus:border-brand-gold focus:ring-brand-gold">
                            @foreach ($periods as $p)
                                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}
                                    {{ $p->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-2">
                            Mengganti periode akan mengubah data yang tampil di dashboard Mahasiswa & Dosen.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-4">

                        <div
                            class="flex items-center justify-between p-4 border rounded-lg {{ $allow_krs ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-slate-200 dark:border-slate-700' }}">
                            <div>
                                <h4
                                    class="font-bold text-sm {{ $allow_krs ? 'text-green-700 dark:text-green-400' : 'text-slate-700 dark:text-slate-300' }}">
                                    Masa Pengisian KRS</h4>
                                <p class="text-xs text-slate-500">Izinkan mahasiswa mengambil mata kuliah?</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="allow_krs" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600">
                                </div>
                            </label>
                        </div>

                        <div
                            class="flex items-center justify-between p-4 border rounded-lg {{ $allow_input_score ? 'border-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'border-slate-200 dark:border-slate-700' }}">
                            <div>
                                <h4
                                    class="font-bold text-sm {{ $allow_input_score ? 'text-orange-700 dark:text-orange-400' : 'text-slate-700 dark:text-slate-300' }}">
                                    Masa Input Nilai</h4>
                                <p class="text-xs text-slate-500">Izinkan dosen mengisi nilai akhir?</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model="allow_input_score" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-orange-500">
                                </div>
                            </label>
                        </div>

                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="px-4 py-2 bg-slate-800 dark:bg-slate-700 text-white rounded hover:bg-slate-700 transition-colors w-full">
                            Simpan Pengaturan Akademik
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
