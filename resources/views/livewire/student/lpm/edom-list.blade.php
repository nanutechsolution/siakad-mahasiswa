<div class="mx-auto max-w-5xl space-y-8">
    <x-slot name="header">Evaluasi Dosen (EDOM)</x-slot>
    <!-- Header Info -->
    <div
        class="rounded-[2.5rem] bg-gradient-to-r from-indigo-600 to-brand-blue p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -mr-16 -mt-16"></div>
        <div class="relative z-10">
            <h2 class="text-2xl font-bold">Evaluasi Kinerja Dosen</h2>
            <p class="text-blue-100 mt-2 max-w-2xl">
                Partisipasi Anda sangat penting untuk meningkatkan kualitas pengajaran.
                Identitas Anda <strong>dirahasiakan</strong>. Silakan isi kuesioner untuk setiap mata kuliah di bawah
                ini sebelum melihat KHS.
            </p>
        </div>
    </div>

    <!-- List Mata Kuliah -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($krs_list as $krs)
            <div
                class="group bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:border-brand-blue transition-all">

                <div class="flex justify-between items-start mb-4">
                    <span
                        class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold px-3 py-1 rounded-full">
                        {{ $krs->classroom->name }}
                    </span>
                    @if ($krs->edom_status == 'DONE')
                        <span class="flex items-center gap-1 text-green-600 font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Selesai
                        </span>
                    @else
                        <span class="flex items-center gap-1 text-red-500 font-bold text-xs animate-pulse">
                            ! Belum Diisi
                        </span>
                    @endif
                </div>

                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">
                    {{ $krs->classroom->course->name }}
                </h3>
                <p class="text-sm text-slate-500 mb-4">
                    {{ $krs->classroom->lecturer->user->name ?? 'Dosen Belum Ditentukan' }}
                </p>

                @if ($krs->edom_status == 'PENDING')
                    <a href="{{ route('student.edom.fill', $krs->classroom_id) }}"
                        class="flex items-center justify-center w-full py-2.5 bg-brand-blue text-white rounded-xl font-bold text-sm hover:bg-blue-800 transition-colors shadow-lg shadow-blue-900/20">
                        Isi Kuesioner
                    </a>
                @else
                    <button disabled
                        class="w-full py-2.5 bg-slate-100 text-slate-400 rounded-xl font-bold text-sm cursor-not-allowed dark:bg-slate-700">
                        Terima Kasih
                    </button>
                @endif
            </div>
        @empty
            <div class="col-span-2 text-center py-12">
                <p class="text-slate-500">Belum ada mata kuliah yang diambil semester ini.</p>
            </div>
        @endforelse
    </div>
</div>
