<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- JIKA USER ADALAH TAMU / BELUM JADI MAHASISWA -->
            @if(Auth::user()->role == 'guest' || Auth::user()->role == 'camaba')
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">Selamat Datang, Calon Mahasiswa!</h3>
                <p class="text-slate-500 mb-6">Langkah selanjutnya adalah melengkapi formulir pendaftaran PMB.</p>
                
                <a href="{{ route('pmb.register') }}" class="inline-flex items-center px-6 py-3 bg-brand-blue border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-800 focus:bg-blue-800 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Isi Formulir Pendaftaran &rarr;
                </a>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>