<div>
    <x-slot name="header">Seleksi Penerimaan Mahasiswa Baru</x-slot>

    <!-- Alert -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200">
            ✅ {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 font-bold border border-red-200">
            ❌ {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <div class="flex gap-2">
            <!-- Filter Status & Prodi (Existing) -->
            <select wire:model.live="filter_status"
                class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white text-sm">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                @endforeach
            </select>

            <!-- TOMBOL EXPORT BARU -->
            <button wire:click="export" wire:loading.attr="disabled"
                class="flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-green-700 transition shadow-sm">
                <span wire:loading.remove wire:target="export">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </span>
                <span wire:loading wire:target="export"
                    class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                Export Data
            </button>
        </div>

        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / No. Daftar..."
            class="rounded-lg border-slate-300 w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
    </div>

    <!-- Table -->
    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">No. Pendaftaran</th>
                    <th class="px-6 py-4">Nama Calon</th>
                    <th class="px-6 py-4">Pilihan Prodi</th>
                    <th class="px-6 py-4">Nilai Rapor</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($registrants as $reg)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-6 py-4 font-mono font-bold text-brand-blue dark:text-brand-gold">
                            {{ $reg->registration_no }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $reg->user->name }}</div>
                            <div class="text-xs text-slate-500">{{ $reg->school_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded text-xs font-bold border border-indigo-100">
                                1. {{ $reg->firstChoice->code }}
                            </span>
                            @if ($reg->secondChoice)
                                <div class="mt-1 text-xs text-slate-400">2. {{ $reg->secondChoice->code }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-700 dark:text-slate-300">
                            {{ $reg->average_grade }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="px-2 py-1 rounded-full text-[10px] font-bold uppercase border
                            {{ match ($reg->status->value) {
                                'DRAFT' => 'bg-slate-100 text-slate-600 border-slate-200',
                                'SUBMITTED' => 'bg-yellow-50 text-yellow-600 border-yellow-200',
                                'VERIFIED' => 'bg-blue-50 text-blue-600 border-blue-200',
                                'ACCEPTED' => 'bg-green-50 text-green-600 border-green-200',
                                'REJECTED' => 'bg-red-50 text-red-600 border-red-200',
                            } }}">
                                {{ $reg->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="showDetail('{{ $reg->id }}')"
                                class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-brand-blue shadow-md transition">
                                Periksa
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada pendaftar masuk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $registrants->links() }}</div>
    </div>

    <!-- MODAL VERIFIKASI -->
    @if ($isModalOpen && $selectedRegistrant)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
            <div
                class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden max-h-[90vh] flex flex-col">

                <div class="p-6 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Verifikasi Pendaftar</h3>
                        <p class="text-sm text-slate-500">{{ $selectedRegistrant->registration_no }} -
                            {{ $selectedRegistrant->user->name }}</p>
                    </div>
                    <button wire:click="$set('isModalOpen', false)"
                        class="text-slate-400 hover:text-red-500 text-2xl">&times;</button>
                </div>

                <div class="p-6 overflow-y-auto flex-1 space-y-6">

                    <!-- Data Diri Grid -->
                    <div
                        class="grid grid-cols-2 md:grid-cols-3 gap-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-600">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase">NIK</p>
                            <p class="font-mono text-sm text-slate-800 dark:text-white">{{ $selectedRegistrant->nik }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase">Asal Sekolah</p>
                            <p class="font-semibold text-sm text-slate-800 dark:text-white">
                                {{ $selectedRegistrant->school_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase">Rata-rata Nilai</p>
                            <p class="font-black text-lg text-brand-blue">{{ $selectedRegistrant->average_grade }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase">Ayah</p>
                            <p class="text-sm text-slate-800 dark:text-white">{{ $selectedRegistrant->father_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase">Ibu</p>
                            <p class="text-sm text-slate-800 dark:text-white">{{ $selectedRegistrant->mother_name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase">Kontak Ortu</p>
                            <p class="text-sm text-slate-800 dark:text-white">{{ $selectedRegistrant->parent_phone }}
                            </p>
                        </div>
                    </div>

                    <!-- Dokumen -->
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-blue" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            Berkas Lampiran
                        </h4>
                        <div class="flex gap-4">
                            @if (isset($selectedRegistrant->documents['ijazah']))
                                <a href="{{ asset('storage/' . $selectedRegistrant->documents['ijazah']) }}"
                                    target="_blank"
                                    class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 rounded-lg border border-red-100 hover:bg-red-100 transition text-sm font-bold">
                                    📄 Lihat Ijazah
                                </a>
                            @endif
                            @if (isset($selectedRegistrant->documents['ktp']))
                                <a href="{{ asset('storage/' . $selectedRegistrant->documents['ktp']) }}" target="_blank"
                                    class="flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-100 hover:bg-blue-100 transition text-sm font-bold">
                                    🆔 Lihat KTP
                                </a>
                            @endif
                        </div>
                    </div>

                </div>

                <!-- Action Footer -->
                <div
                    class="p-6 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 flex justify-end gap-3">

                    @if ($selectedRegistrant->status == \App\Enums\RegistrantStatus::SUBMITTED)
                        <button wire:click="reject" wire:confirm="Tolak pendaftar ini?"
                            class="px-4 py-2 rounded-lg border border-red-200 text-red-600 font-bold hover:bg-red-50">
                            Tolak (Berkas Tidak Sesuai)
                        </button>
                        <button wire:click="verify" wire:confirm="Verifikasi berkas valid?"
                            class="px-6 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 font-bold shadow-lg">
                            Verifikasi Berkas
                        </button>
                    @elseif($selectedRegistrant->status == \App\Enums\RegistrantStatus::VERIFIED)
                        <button wire:click="reject"
                            class="px-4 py-2 rounded-lg border border-red-200 text-red-600 font-bold hover:bg-red-50">Gagal
                            Seleksi</button>
                        <button wire:click="accept" wire:confirm="Nyatakan LULUS SELEKSI?"
                            class="px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 font-bold shadow-lg">
                            NYATAKAN LULUS
                        </button>
                    @elseif($selectedRegistrant->status == \App\Enums\RegistrantStatus::ACCEPTED)
                        <div class="flex items-center gap-4">
                            <span class="text-green-600 font-bold text-sm">✅ Sudah Lulus Seleksi</span>
                            <button wire:click="promoteToStudent('{{ $selectedRegistrant->id }}')"
                                wire:confirm="Proses Daftar Ulang? Akun akan berubah menjadi MAHASISWA dan mendapatkan NIM."
                                class="px-6 py-2 rounded-lg bg-brand-gold text-brand-blue hover:bg-yellow-400 font-black shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                GENERATE NIM & AKTIFKAN
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif
</div>
