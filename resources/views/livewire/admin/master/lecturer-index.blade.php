<div>
    <x-slot name="header">Data Dosen</x-slot>

    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / NIDN..." class="rounded-lg border-slate-300 w-full md:w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
        <button wire:click="create" class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition">
            + Tambah Dosen
        </button>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Nama Lengkap</th>
                    <th class="px-6 py-4">NIDN / NIP</th>
                    <th class="px-6 py-4">Homebase</th>
                    <th class="px-6 py-4">Kontak</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($lecturers as $l)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white">
                            {{ $l->front_title }} {{ $l->user->name }} {{ $l->back_title }}
                        </div>
                        <div class="text-xs text-slate-500">{{ $l->user->email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-mono text-xs">
                            @if($l->nidn) <div>NIDN: {{ $l->nidn }}</div> @endif
                            @if($l->nip_internal) <div class="text-slate-400">NIP: {{ $l->nip_internal }}</div> @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold dark:bg-indigo-900/50 dark:text-indigo-300">
                            {{ $l->study_program->name ?? '-' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $l->phone ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button wire:click="edit('{{ $l->id }}')" class="text-blue-600 hover:underline font-medium">Edit</button>
                        <button wire:click="delete('{{ $l->id }}')" wire:confirm="Hapus data dosen ini?" class="text-red-600 hover:underline font-medium">Hapus</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">{{ $lecturers->links() }}</div>
    </div>

    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-2xl my-8">
            <div class="p-6 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                    {{ $isEditMode ? 'Edit Data Dosen' : 'Registrasi Dosen Baru' }}
                </h3>
            </div>
            
            <form wire:submit.prevent="store" class="p-6 space-y-4">
                
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-3">
                        <label class="block text-xs font-bold mb-1 dark:text-slate-300">Gelar Depan</label>
                        <input wire:model="front_title" type="text" placeholder="Dr." class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    </div>
                    <div class="col-span-6">
                        <label class="block text-xs font-bold mb-1 dark:text-slate-300">Nama Lengkap (Tanpa Gelar)</label>
                        <input wire:model="name" type="text" placeholder="Budi Santoso" class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-span-3">
                        <label class="block text-xs font-bold mb-1 dark:text-slate-300">Gelar Blkg</label>
                        <input wire:model="back_title" type="text" placeholder="S.Kom., M.T." class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1 dark:text-slate-300">NIDN (Nasional)</label>
                        <input wire:model="nidn" type="text" class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        @error('nidn') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1 dark:text-slate-300">NIP Internal (Kampus)</label>
                        <input wire:model="nip_internal" type="text" class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        @error('nip_internal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold mb-1 dark:text-slate-300">Homebase Prodi</label>
                        <select wire:model="study_program_id" class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            <option value="">-- Pilih Prodi --</option>
                            @foreach($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->degree }})</option>
                            @endforeach
                        </select>
                        @error('study_program_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold mb-1 dark:text-slate-300">No. WhatsApp</label>
                        <input wire:model="phone" type="text" class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700 pt-4 mt-2">
                    <h4 class="text-sm font-bold text-slate-500 mb-3 uppercase tracking-wider">Akun Login</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold mb-1 dark:text-slate-300">Email Aktif</label>
                            <input wire:model="email" type="email" class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1 dark:text-slate-300">
                                Password 
                                @if($isEditMode) <span class="text-slate-400 font-normal">(Isi jika ingin ubah)</span> @endif
                            </label>
                            <input wire:model="password" type="text" class="w-full rounded border-slate-300 text-sm dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Min 6 karakter">
                            @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded text-slate-500 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-700">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-lg shadow-blue-500/30">
                        {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Dosen' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>