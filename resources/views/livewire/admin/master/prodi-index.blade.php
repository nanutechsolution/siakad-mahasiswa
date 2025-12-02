<div>
    <x-slot name="header">Master Program Studi</x-slot>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">{{ session('message') }}</div>
    @endif

    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Prodi..." class="rounded-lg border-slate-300 w-full md:w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
        <button wire:click="create" class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 shadow-lg">+ Tambah Prodi</button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Kode</th>
                    <th class="px-6 py-4">Jenjang</th>
                    <th class="px-6 py-4">Program Studi</th>
                    <th class="px-6 py-4 text-center">Target SKS</th> <!-- Kolom Baru -->
                    <th class="px-6 py-4">Kaprodi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach ($prodis as $p)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-6 py-4 font-mono font-bold text-brand-blue dark:text-brand-gold">{{ $p->code }}</td>
                        <td class="px-6 py-4"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">{{ $p->degree }}</span></td>
                        <td class="px-6 py-4 font-bold text-slate-800 dark:text-white">
                            {{ $p->name }}
                            <div class="text-xs text-slate-500 font-normal">{{ $p->faculty->name ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 text-center font-mono text-slate-600 dark:text-slate-400">{{ $p->total_credits }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-slate-800 dark:text-white">{{ $p->head_name ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $p->head_nip }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button wire:click="edit({{ $p->id }})" class="text-blue-600 hover:underline font-medium mr-2">Edit</button>
                            <button wire:click="delete({{ $p->id }})" wire:confirm="Hapus?" class="text-red-600 hover:underline font-medium">Hapus</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $prodis->links() }}</div>
    </div>

    <!-- Modal Form -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $isEditMode ? 'Edit Prodi' : 'Tambah Prodi' }}</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Fakultas</label>
                        <select wire:model="faculty_id" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach(\App\Models\Faculty::all() as $f)
                                <option value="{{ $f->id }}">{{ $f->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Kode</label>
                            <input wire:model="code" type="text" class="w-full rounded-lg border-slate-300 text-sm uppercase dark:bg-slate-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Jenjang</label>
                            <select wire:model="degree" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white">
                                <option value="S1">S1 (Sarjana)</option>
                                <option value="D3">D3 (Diploma)</option>
                                <option value="S2">S2 (Magister)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Nama Prodi</label>
                            <input wire:model="name" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white">
                        </div>
                        <!-- INPUT TARGET SKS -->
                        <div>
                            <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Total SKS</label>
                            <input wire:model="total_credits" type="number" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white">
                        </div>
                    </div>

                    <div class="pt-4 mt-2 border-t border-slate-100 dark:border-slate-700">
                        <label class="block text-xs font-bold text-brand-blue mb-3 uppercase">Pejabat Prodi</label>
                        <div class="space-y-3">
                            <input wire:model="head_name" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white" placeholder="Nama Kaprodi">
                            <input wire:model="head_nip" type="text" class="w-full rounded-lg border-slate-300 text-sm dark:bg-slate-700 dark:text-white" placeholder="NIP / NIDN">
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-6 pt-2 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700">
                    <button wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded-lg text-slate-500 hover:bg-slate-200">Batal</button>
                    <button wire:click="store" class="px-6 py-2 rounded-lg bg-brand-blue text-white hover:bg-blue-800 shadow-md">Simpan</button>
                </div>
            </div>
        </div>
    @endif
</div>