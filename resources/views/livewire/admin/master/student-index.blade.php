<div>
    <x-slot name="header">Data Mahasiswa</x-slot>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / NIM..."
            class="rounded-lg border-slate-300 w-full md:w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
        <button wire:click="create"
            class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition">
            + Daftar Mahasiswa
        </button>
    </div>

    <div
        class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Mahasiswa</th>
                    <th class="px-6 py-4">Prodi / Angkatan</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach ($students as $s)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $s->user->name }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $s->nim }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div>{{ $s->study_program->name ?? '-' }}</div>
                            <div class="text-xs text-slate-400">Angkatan {{ $s->entry_year }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusLabel = [
                                    'A' => ['Aktif', 'bg-green-100 text-green-800'],
                                    'C' => ['Cuti', 'bg-yellow-100 text-yellow-800'],
                                    'D' => ['Drop Out', 'bg-red-100 text-red-800'],
                                    'L' => ['Lulus', 'bg-blue-100 text-blue-800'],
                                    'N' => ['Non-Aktif', 'bg-gray-100 text-gray-800'],
                                ];
                                $st = $statusLabel[$s->status] ?? ['Unknown', 'bg-gray-100'];
                            @endphp
                            <span
                                class="{{ $st[1] }} px-2 py-1 rounded text-xs font-bold">{{ $st[0] }}</span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button wire:click="edit('{{ $s->id }}')"
                                class="text-blue-600 hover:underline font-medium">Edit</button>
                            <button wire:click="delete('{{ $s->id }}')" wire:confirm="Hapus data mahasiswa ini?"
                                class="text-red-600 hover:underline font-medium">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $students->links() }}</div>
    </div>

    @if ($isModalOpen)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl my-8">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                        {{ $isEditMode ? 'Edit Data Mahasiswa' : 'Registrasi Mahasiswa Baru' }}
                    </h3>
                </div>

                <form wire:submit.prevent="store" class="p-6 space-y-6">

                    <div>
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-brand-blue mb-3 border-b pb-1 dark:text-brand-gold">
                            Informasi Akun</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Nama Lengkap</label>
                                <input wire:model="name" type="text"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                @error('name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Email</label>
                                <input wire:model="email" type="email"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                @error('email')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Password</label>
                                <input wire:model="password" type="text"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white"
                                    placeholder="{{ $isEditMode ? 'Kosongkan jika tetap' : 'Min 6 karakter' }}">
                                @error('password')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-brand-blue mb-3 border-b pb-1 dark:text-brand-gold">
                            Data Akademik</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">NIM</label>
                                <input wire:model="nim" type="text"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                @error('nim')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Program Studi</label>
                                <select wire:model="prodi_id"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    <option value="">Pilih Prodi</option>
                                    @foreach ($prodis as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('prodi_id')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Tahun Masuk</label>
                                <input wire:model="entry_year" type="number"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Status</label>
                                <select wire:model="status"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    <option value="A">Aktif</option>
                                    <option value="C">Cuti</option>
                                    <option value="N">Non-Aktif</option>
                                    <option value="L">Lulus</option>
                                    <option value="D">Drop Out</option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-bold mb-1 dark:text-slate-300">Dosen Wali (Academic
                            Advisor)</label>
                        <select wire:model="academic_advisor_id"
                            class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">-- Pilih Dosen Wali --</option>
                            @foreach ($lecturers as $dosen)
                                <option value="{{ $dosen->id }}">
                                    {{ $dosen->user->name }} ({{ $dosen->nidn }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <h4
                            class="text-xs font-bold uppercase tracking-wider text-brand-blue mb-3 border-b pb-1 dark:text-brand-gold">
                            Biodata</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Tempat Lahir</label>
                                <input wire:model="pob" type="text"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Tanggal Lahir</label>
                                <input wire:model="dob" type="date"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Jenis Kelamin</label>
                                <select wire:model="gender"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                    <option value="">Pilih</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                                @error('gender')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">No. WhatsApp</label>
                                <input wire:model="phone" type="text"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-xs font-bold mb-1 dark:text-slate-300">Alamat Domisili</label>
                                <textarea wire:model="address" rows="2"
                                    class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-4">
                        <button type="button" wire:click="$set('isModalOpen', false)"
                            class="px-4 py-2 rounded text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 rounded bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-lg shadow-blue-500/30">
                            {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Mahasiswa' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
