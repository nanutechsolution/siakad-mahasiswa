<div>
    <x-slot name="header">Manajemen Pengguna (User)</x-slot>

    <!-- Alert -->
    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200 shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-100 text-red-700 font-bold border border-red-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col md:flex-row justify-between mb-6 gap-4">
        <div class="flex gap-2">
            <select wire:model.live="filter_role" class="rounded-lg border-slate-300 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
                <option value="">Semua Role</option>
                <option value="admin">Administrator</option>
                <option value="lecturer">Dosen</option>
                <option value="student">Mahasiswa</option>
                <option value="camaba">Camaba</option>
            </select>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / Email / Username..." class="rounded-lg border-slate-300 w-64 dark:bg-slate-800 dark:border-slate-700 dark:text-white">
        </div>
        
        <button wire:click="create" class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow-lg">
            + Tambah User Admin/Staff
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700 text-slate-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Nama User</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Kontak</th>
                    <th class="px-6 py-4 text-center">Status</th>
                    <th class="px-6 py-4 text-right">Aksi Cepat</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-900 dark:text-white">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500 font-mono">{{ $user->username }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $colors = [
                                'admin' => 'bg-purple-100 text-purple-700',
                                'lecturer' => 'bg-blue-100 text-blue-700',
                                'student' => 'bg-green-100 text-green-700',
                                'camaba' => 'bg-yellow-100 text-yellow-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $colors[$user->role] ?? 'bg-gray-100' }}">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $user->email }}
                    </td>
                    <td class="px-6 py-4 text-center">
                         <button wire:click="toggleStatus('{{ $user->id }}')" 
                                 class="px-3 py-1 rounded-full text-[10px] font-bold transition-all {{ $user->is_active ? 'bg-green-50 text-green-600 hover:bg-green-100' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                            {{ $user->is_active ? 'AKTIF' : 'BLOCKED' }}
                        </button>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <!-- Reset Password -->
                            <button wire:click="resetPassword('{{ $user->id }}')" 
                                    wire:confirm="Reset password user ini menjadi '12345678'?"
                                    class="p-2 text-orange-500 hover:bg-orange-50 rounded-lg" title="Reset Password ke 12345678">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                            </button>
                            
                            <!-- Edit -->
                            <button wire:click="edit('{{ $user->id }}')" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </button>

                            <!-- Delete -->
                            @if($user->id !== auth()->id())
                            <button wire:click="delete('{{ $user->id }}')" wire:confirm="Hapus user ini? Data terkait (mahasiswa/dosen) mungkin ikut terhapus." class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">Tidak ada user ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $users->links() }}</div>
    </div>

    <!-- MODAL FORM -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-xl font-bold mb-4 text-slate-900 dark:text-white">{{ $isEditMode ? 'Edit User' : 'Tambah User Admin/Staff' }}</h3>
            
            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-slate-300">Nama Lengkap</label>
                    <input wire:model="name" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-slate-300">Email</label>
                    <input wire:model="email" type="email" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-slate-300">Role</label>
                    <select wire:model="role" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin">Administrator</option>
                        <option value="lecturer">Dosen</option>
                        <option value="student">Mahasiswa</option>
                    </select>
                    @error('role') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1 dark:text-slate-300">
                        Password 
                        @if($isEditMode) <span class="font-normal text-slate-400 text-xs">(Isi jika ingin ganti)</span> @endif
                    </label>
                    <input wire:model="password" type="text" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:border-slate-600 dark:text-white" placeholder="Min 6 karakter">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t dark:border-slate-700">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded text-slate-500 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded bg-brand-blue text-white hover:bg-blue-800 font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>