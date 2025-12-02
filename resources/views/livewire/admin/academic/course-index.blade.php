<div>
    <x-slot name="header">Manajemen Mata Kuliah</x-slot>

    <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-1 gap-4">
            <div class="relative w-full max-w-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="block w-full rounded-lg border border-slate-300 bg-white p-2.5 pl-10 text-sm text-slate-900 focus:border-brand-blue focus:ring-brand-blue dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:placeholder-slate-400"
                    placeholder="Cari Kode / Nama Matkul...">
            </div>

            <select wire:model.live="filter_prodi"
                class="rounded-lg border border-slate-300 bg-white p-2.5 text-sm text-slate-900 focus:border-brand-blue focus:ring-brand-blue dark:border-slate-600 dark:bg-slate-800 dark:text-white">
                <option value="">Semua Prodi</option>
                @foreach ($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->degree }})</option>
                @endforeach
            </select>
        </div>

        <button wire:click="create"
            class="flex items-center gap-2 rounded-lg bg-brand-blue px-4 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Matkul
        </button>
    </div>

    <div class="relative overflow-x-auto rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
        <table class="w-full text-left text-sm text-slate-500 dark:text-slate-400">
            <thead class="bg-slate-50 text-xs uppercase text-slate-700 dark:bg-slate-700 dark:text-slate-200">
                <tr>
                    <th scope="col" class="px-6 py-3">Kode</th>
                    <th scope="col" class="px-6 py-3">Nama Mata Kuliah</th>
                    <th scope="col" class="px-6 py-3">SKS</th>
                    <th scope="col" class="px-6 py-3">Smt</th>
                    <th scope="col" class="px-6 py-3">Prodi</th>
                    <th scope="col" class="px-6 py-3">Status</th>
                    <th scope="col" class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($courses as $c)
                    <tr
                        class="border-b bg-white hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700">
                        <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900 dark:text-white">
                            {{ $c->code }}</td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-800 dark:text-slate-200">{{ $c->name }}</div>
                            <div class="text-xs text-slate-500">English: {{ $c->name_en ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-brand-blue dark:text-brand-gold">{{ $c->credit_total }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $c->semester_default }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="rounded bg-indigo-100 px-2.5 py-0.5 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300">
                                {{ $c->study_program->code ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($c->is_active)
                                <span
                                    class="rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">Aktif</span>
                            @else
                                <span
                                    class="rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-300">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $c->id }})"
                                class="font-medium text-blue-600 hover:underline dark:text-blue-500">Edit</button>
                            <button wire:confirm="Yakin hapus matkul ini?" wire:click="delete({{ $c->id }})"
                                class="ml-3 font-medium text-red-600 hover:underline dark:text-red-500">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                            Belum ada data mata kuliah. Silakan tambah baru.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $courses->links() }}
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-2xl rounded-xl bg-white p-6 shadow-2xl dark:bg-slate-800">
                <h3 class="mb-4 text-xl font-bold text-slate-900 dark:text-white">
                    {{ $isEditMode ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah Baru' }}
                </h3>

                <form wire:submit.prevent="store">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div class="col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-900 dark:text-white">Program
                                Studi</label>
                            <select wire:model="study_program_id"
                                class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-brand-blue focus:ring-brand-blue dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                                <option value="">Pilih Prodi...</option>
                                @foreach ($prodis as $p)
                                    <option value="{{ $p->id }}">{{ $p->degree }} - {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('study_program_id')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-900 dark:text-white">Kode
                                Matkul</label>
                            <input wire:model="code" type="text" placeholder="Contoh: TI-101"
                                class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-brand-blue focus:ring-brand-blue dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            @error('code')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-900 dark:text-white">Semester
                                Paket</label>
                            <input wire:model="semester_default" type="number" placeholder="1-8"
                                class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-brand-blue focus:ring-brand-blue dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            @error('semester_default')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-900 dark:text-white">Nama Mata
                                Kuliah</label>
                            <input wire:model="name" type="text"
                                class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-brand-blue focus:ring-brand-blue dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            @error('name')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-900 dark:text-white">Total
                                SKS</label>
                            <input wire:model="credit_total" type="number"
                                class="block w-full rounded-lg border border-slate-300 bg-slate-50 p-2.5 text-sm text-slate-900 focus:border-brand-blue focus:ring-brand-blue dark:border-slate-600 dark:bg-slate-700 dark:text-white">
                            @error('credit_total')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer mt-6">
                                <input type="checkbox" wire:model="is_active" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-blue">
                                </div>
                                <span class="ml-3 text-sm font-medium text-slate-900 dark:text-slate-300">Status
                                    Aktif</span>
                            </label>
                        </div>

                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg">

                        <div>
                            <label
                                class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Kelompok</label>
                            <select wire:model="group_code"
                                class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-brand-blue focus:border-brand-blue block w-full p-2.5 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                                <option value="MKU">MKU (Umum)</option>
                                <option value="MKK">MKK (Keilmuan)</option>
                                <option value="MKB">MKB (Berkarya)</option>
                                <option value="MPK">MPK (Pengembangan)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-slate-900 dark:text-white">Sifat Mata
                                Kuliah</label>
                            <div class="flex items-center gap-4 mt-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" wire:model="is_mandatory" value="1"
                                        class="w-4 h-4 text-brand-blue bg-gray-100 border-gray-300 focus:ring-brand-blue dark:bg-gray-700">
                                    <span
                                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Wajib</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" wire:model="is_mandatory" value="0"
                                        class="w-4 h-4 text-brand-blue bg-gray-100 border-gray-300 focus:ring-brand-blue dark:bg-gray-700">
                                    <span
                                        class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Pilihan</span>
                                </label>
                            </div>
                        </div>
                    </div>  

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="closeModal"
                            class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-700 dark:text-white dark:hover:bg-slate-600">Batal</button>
                        <button type="submit"
                            class="rounded-lg bg-brand-blue px-5 py-2.5 text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-900">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
