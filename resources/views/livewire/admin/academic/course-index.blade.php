<div class="space-y-6 font-sans">
    <x-slot name="header">Master Mata Kuliah</x-slot>

    {{-- ALERT --}}
    @if (session()->has('message'))
        <div class="p-4 rounded-xl bg-green-100 text-green-700 font-bold border border-green-200 flex items-center gap-2 shadow">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    {{-- TOOLBAR --}}
    <div class="flex flex-col sm:flex-row gap-4 justify-between bg-white p-4 rounded-2xl shadow border">
        <div class="relative w-full sm:max-w-md">
            <input wire:model.live.debounce.300ms="search"
                   type="text"
                   placeholder="Cari kode atau nama mata kuliah..."
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border focus:ring-brand-blue">
            <div class="absolute left-3 top-2.5 text-slate-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <button wire:click="create"
                class="px-6 py-2.5 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800 shadow flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Mata Kuliah
        </button>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-[2rem] shadow border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-[10px] uppercase tracking-widest font-bold">
                    <tr>
                        <th class="px-6 py-4">Mata Kuliah</th>
                        <th class="px-6 py-4 text-center">SKS</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($courses as $course)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex gap-3 items-center">
                                    <div class="w-11 h-11 rounded-xl bg-slate-100 border flex flex-col justify-center items-center text-[10px] font-bold">
                                        <span class="text-brand-blue">{{ $course->code }}</span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $course->name }}</div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $course->credit_theory }}T • {{ $course->credit_practice }}P
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-black">{{ $course->credit_total }}</span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($course->is_active)
                                    <span class="px-3 py-1 text-[10px] bg-green-100 text-green-700 border rounded-full font-bold">
                                        AKTIF
                                    </span>
                                @else
                                    <span class="px-3 py-1 text-[10px] bg-slate-100 text-slate-500 border rounded-full font-bold">
                                        NON-AKTIF
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <button wire:click="edit('{{ $course->id }}')"
                                            class="p-2 rounded-lg hover:bg-blue-50 text-slate-500 hover:text-brand-blue">
                                        ✏️
                                    </button>
                                    <button wire:click="delete('{{ $course->id }}')"
                                            wire:confirm="Arsipkan mata kuliah ini?"
                                            class="p-2 rounded-lg hover:bg-red-50 text-slate-500 hover:text-red-600">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                                Belum ada data mata kuliah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 bg-slate-50 border-t">
            {{ $courses->links() }}
        </div>
    </div>

    {{-- MODAL CRUD --}}
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center p-4">
            <div class="bg-white w-full max-w-xl rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

                {{-- HEADER --}}
                <div class="px-6 py-4 border-b bg-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-lg">
                        {{ $isEditMode ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}
                    </h3>
                    <button wire:click="closeModal" class="text-slate-400 hover:text-red-500 text-xl">✕</button>
                </div>

                {{-- FORM --}}
                <form wire:submit.prevent="store" class="p-6 space-y-5">

                    <div>
                        <label class="text-sm font-bold">Kode Mata Kuliah</label>
                        <input wire:model="code"
                               class="w-full rounded-xl border uppercase focus:ring-brand-blue">
                        @error('code') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="text-sm font-bold">Nama Mata Kuliah</label>
                        <input wire:model="name"
                               class="w-full rounded-xl border focus:ring-brand-blue">
                        @error('name') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs font-bold">Total SKS</label>
                            <input wire:model="credit_total" type="number"
                                   class="w-full rounded-xl border text-center font-bold">
                        </div>
                        <div>
                            <label class="text-xs font-bold">SKS Teori</label>
                            <input wire:model="credit_theory" type="number"
                                   class="w-full rounded-xl border text-center">
                        </div>
                        <div>
                            <label class="text-xs font-bold">SKS Praktik</label>
                            <input wire:model="credit_practice" type="number"
                                   class="w-full rounded-xl border text-center">
                        </div>
                    </div>

                    <label class="inline-flex items-center gap-3">
                        <input type="checkbox" wire:model="is_active"
                               class="rounded text-brand-blue focus:ring-brand-blue">
                        <span class="font-bold text-sm">Mata Kuliah Aktif</span>
                    </label>

                    {{-- FOOTER --}}
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" wire:click="closeModal"
                                class="px-5 py-2 rounded-xl font-bold text-slate-500 hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-6 py-2 bg-brand-blue text-white rounded-xl font-bold hover:bg-blue-800">
                            Simpan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endif
</div>
