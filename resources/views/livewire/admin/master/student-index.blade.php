<div class="space-y-6 font-sans">
    <x-slot name="header">Data Mahasiswa</x-slot>

    <!-- Alert Messages -->
    @if (session()->has('message'))
        <div class="mb-4 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 animate-fade-in-down">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="mb-4 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 animate-pulse">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Toolbar Responsive -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="relative w-full sm:w-64">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari Nama / NIM..."
                class="w-full pl-10 rounded-xl border-slate-200 dark:bg-slate-900 dark:border-slate-700 dark:text-white focus:ring-brand-blue transition-all">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <button wire:click="create"
            class="w-full sm:w-auto bg-brand-blue text-white px-6 py-2.5 rounded-xl text-sm font-bold hover:bg-blue-800 transition shadow-lg shadow-blue-900/20 transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Mahasiswa
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-800 rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 uppercase text-[10px] font-black tracking-widest border-b border-slate-100 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4">Mahasiswa</th>
                        <th class="px-6 py-4">Prodi / Angkatan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($students as $s)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs">
                                        {{ substr($s->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $s->user->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">NIM: {{ $s->nim }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $s->study_program->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Angkatan {{ $s->entry_year }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $statusLabel = [
                                        'A' => ['Aktif', 'bg-green-100 text-green-700 border-green-200'],
                                        'C' => ['Cuti', 'bg-yellow-100 text-yellow-700 border-yellow-200'],
                                        'D' => ['Drop Out', 'bg-red-100 text-red-700 border-red-200'],
                                        'L' => ['Lulus', 'bg-blue-100 text-blue-700 border-blue-200'],
                                        'N' => ['Non-Aktif', 'bg-gray-100 text-gray-700 border-gray-200'],
                                    ];
                                    $st = $statusLabel[$s->status] ?? ['Unknown', 'bg-gray-100 text-gray-500'];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $st[1] }} uppercase tracking-wider">
                                    {{ $st[0] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="edit('{{ $s->id }}')" class="text-brand-blue hover:text-blue-700 font-bold text-xs hover:underline">Edit</button>
                                <button wire:click="delete('{{ $s->id }}')" wire:confirm="Hapus data mahasiswa ini?" class="text-red-500 hover:text-red-700 font-bold text-xs hover:underline">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data mahasiswa.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-6 bg-slate-50 dark:bg-slate-900 border-t border-slate-100 dark:border-slate-700">
            {{ $students->links() }}
        </div>
    </div>

    <!-- RESPONSIVE MODAL -->
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto">
            <!-- Container: Bottom sheet di HP, Card di Desktop -->
            <div class="bg-white dark:bg-slate-800 rounded-t-[2rem] sm:rounded-[2rem] shadow-2xl w-full max-w-3xl mx-auto transform transition-all scale-100 overflow-hidden flex flex-col max-h-[95vh] sm:max-h-[90vh]">
                
                <!-- Header -->
                <div class="px-6 sm:px-8 py-5 sm:py-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-700/50 flex-shrink-0 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">
                            {{ $isEditMode ? 'Edit Data Mahasiswa' : 'Registrasi Mahasiswa Baru' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">Lengkapi data akademik dan biodata.</p>
                    </div>
                    <button wire:click="$set('isModalOpen', false)" class="p-2 hover:bg-red-50 text-slate-400 hover:text-red-500 rounded-full transition text-xl">&times;</button>
                </div>

                <!-- Scrollable Content -->
                <div class="overflow-y-auto p-6 sm:p-8 space-y-8">
                    <form wire:submit.prevent="store" id="studentForm">
                        
                        <!-- SECTION 1: AKUN -->
                        <div class="mb-8">
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
                                <span class="w-6 h-[1px] bg-slate-300"></span> Informasi Akun
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="col-span-1 sm:col-span-2">
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Nama Lengkap</label>
                                    <input wire:model="name" type="text" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                    @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Email</label>
                                    <input wire:model="email" type="email" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                    @error('email') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Password</label>
                                    <input wire:model="password" type="password" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue"
                                        placeholder="{{ $isEditMode ? 'Biarkan kosong jika tidak diubah' : 'Min. 6 karakter' }}">
                                    @error('password') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: AKADEMIK -->
                        <div class="mb-8">
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
                                <span class="w-6 h-[1px] bg-slate-300"></span> Data Akademik
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">NIM</label>
                                    <input wire:model="nim" type="text" class="w-full rounded-xl border-slate-300 text-sm font-mono font-bold focus:ring-brand-blue">
                                    @error('nim') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Program Studi</label>
                                    <select wire:model="prodi_id" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                        <option value="">-- Pilih Prodi --</option>
                                        @foreach ($prodis as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('prodi_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Tahun Masuk</label>
                                    <input wire:model="entry_year" type="number" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                    @error('entry_year') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- Status dengan Error Message Khusus -->
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Status Mahasiswa</label>
                                    <select wire:model="status" class="w-full rounded-xl border-slate-300 text-sm font-bold focus:ring-brand-blue">
                                        <option value="A">Aktif</option>
                                        <option value="C">Cuti</option>
                                        <option value="N">Non-Aktif</option>
                                        <option value="L">Lulus</option>
                                        <option value="D">Drop Out</option>
                                    </select>
                                    <!-- Menampilkan Error Validasi Status (misal ada tagihan) -->
                                    @error('status') 
                                        <div class="mt-2 p-2 bg-red-50 border border-red-100 rounded text-xs text-red-600 font-bold flex items-start gap-2">
                                            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-span-1 sm:col-span-2">
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Dosen Wali</label>
                                    <select wire:model="academic_advisor_id" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                        <option value="">-- Pilih Dosen Wali --</option>
                                        @foreach ($lecturers as $dosen)
                                            <option value="{{ $dosen->id }}">{{ $dosen->user->name }} ({{ $dosen->nidn }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 3: BIODATA -->
                        <div>
                            <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 flex items-center gap-2">
                                <span class="w-6 h-[1px] bg-slate-300"></span> Biodata Diri
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Tempat Lahir</label>
                                    <input wire:model="pob" type="text" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Tanggal Lahir</label>
                                    <input wire:model="dob" type="date" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Jenis Kelamin</label>
                                    <div class="flex gap-4 mt-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" wire:model="gender" value="L" class="text-brand-blue focus:ring-brand-blue">
                                            <span class="text-sm">Laki-laki</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" wire:model="gender" value="P" class="text-pink-500 focus:ring-pink-500">
                                            <span class="text-sm">Perempuan</span>
                                        </label>
                                    </div>
                                    @error('gender') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">No. WhatsApp</label>
                                    <input wire:model="phone" type="text" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue">
                                </div>
                                <div class="col-span-1 sm:col-span-2">
                                    <label class="block text-xs font-bold mb-1 text-slate-700 dark:text-slate-300">Alamat Domisili</label>
                                    <textarea wire:model="address" rows="2" class="w-full rounded-xl border-slate-300 text-sm focus:ring-brand-blue"></textarea>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>

                <!-- Footer Action -->
                <div class="px-6 sm:px-8 py-5 sm:py-6 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3 flex-shrink-0 bg-slate-50 dark:bg-slate-800">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-6 py-2.5 rounded-xl text-slate-600 hover:bg-slate-200 font-bold transition text-sm">Batal</button>
                    <button type="submit" form="studentForm" wire:loading.attr="disabled"
                        class="px-8 py-2.5 rounded-xl bg-brand-blue text-white hover:bg-blue-800 font-bold shadow-lg shadow-blue-900/20 transition flex items-center gap-2 disabled:opacity-50 text-sm">
                        <span wire:loading.remove>Simpan Data</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>