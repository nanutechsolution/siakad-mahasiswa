<div>
    <x-slot name="header">Instrumen EDOM (Evaluasi Dosen)</x-slot>

    @if (session()->has('message'))
        <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-700 font-bold border border-green-200">
            ✅ {{ session('message') }}
        </div>
    @endif

    <div class="flex justify-end mb-6">
        <button wire:click="create" class="bg-brand-blue text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-800 transition shadow-lg">
            + Tambah Pertanyaan
        </button>
    </div>

    <div class="space-y-8">
        @foreach($questions as $category => $list)
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="bg-slate-50 dark:bg-slate-700 px-6 py-3 border-b border-slate-200 dark:border-slate-600">
                <h3 class="font-bold text-slate-800 dark:text-white uppercase tracking-wider text-sm">{{ $category }}</h3>
            </div>
            <table class="w-full text-left text-sm">
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($list as $q)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                        <td class="px-6 py-4 w-12 text-center text-slate-400">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-medium text-slate-700 dark:text-slate-300">
                            {{ $q->question_text }}
                            @if(!$q->is_active)
                                <span class="ml-2 text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right w-32">
                            <button wire:click="edit({{ $q->id }})" class="text-blue-600 hover:underline mr-3">Edit</button>
                            <button wire:click="delete({{ $q->id }})" wire:confirm="Hapus pertanyaan ini?" class="text-red-600 hover:underline">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>

    <!-- MODAL FORM -->
    @if($isModalOpen)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl w-full max-w-lg p-6">
            <h3 class="text-xl font-bold mb-4 text-slate-900 dark:text-white">{{ $isEditMode ? 'Edit Pertanyaan' : 'Tambah Pertanyaan' }}</h3>
            
            <form wire:submit.prevent="store" class="space-y-4">
                <div>
                    <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Kategori</label>
                    <select wire:model="category" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white">
                        <option>Pedagogik</option>
                        <option>Profesional</option>
                        <option>Kepribadian</option>
                        <option>Sosial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1 text-slate-700 dark:text-slate-300">Isi Pertanyaan</label>
                    <textarea wire:model="question_text" rows="3" class="w-full rounded-lg border-slate-300 dark:bg-slate-700 dark:text-white"></textarea>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="is_active" class="rounded text-brand-blue focus:ring-brand-blue">
                    <span class="text-sm text-slate-700 dark:text-slate-300">Aktifkan Pertanyaan</span>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 rounded text-slate-500 hover:bg-slate-100">Batal</button>
                    <button type="submit" class="px-6 py-2 rounded bg-brand-blue text-white hover:bg-blue-800 font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>