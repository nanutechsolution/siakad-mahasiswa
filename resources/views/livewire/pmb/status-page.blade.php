<div class="max-w-4xl mx-auto pt-24 pb-20 px-6 font-sans">

    @if (session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-center font-bold">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-slate-900 dark:text-white">Halo, {{ Auth::user()->name }}</h2>
        <p class="text-slate-500 mt-2">Status pendaftaran Anda:</p>
    </div>
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
        <div class="p-10 text-center border-b border-slate-100 dark:border-slate-800">
            @php $status = $registrant->status; @endphp
            @if ($status == \App\Enums\RegistrantStatus::ACCEPTED)
                <div
                    class="inline-flex items-center gap-3 px-6 py-3 rounded-full bg-green-100 text-green-800 font-bold text-sm">
                    🎉 DITERIMA
                </div>
                <p class="mt-4 text-sm text-slate-600">Selamat! Anda diterima. Lanjut ke daftar ulang & pembayaran SPP.
                </p>
                <div class="mt-6">
                    <a href="{{ route('pmb.payment') }}"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 transition">
                        Lanjut Daftar Ulang & Pembayaran &rarr;
                    </a>
                </div>
            @endif
        </div>
        <div class="p-6 text-left text-slate-600 text-sm">
            <h4 class="font-bold mb-2">Langkah Selanjutnya:</h4>
            <ol class="list-decimal list-inside space-y-1">
                <li>Transfer SPP sesuai tagihan.</li>
                <li>Unggah bukti transfer di halaman pembayaran.</li>
                <li>Tunggu verifikasi dari bagian keuangan.</li>
                <li>Setelah verifikasi, NIM aktif dan bisa KRS & mengikuti perkuliahan.</li>
            </ol>
        </div>
    </div>
</div>
