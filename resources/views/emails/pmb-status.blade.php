<!DOCTYPE html>
<html>

<head>
    <title>Update Status PMB</title>
</head>

<body style="font-family: sans-serif; color: #333;">
    <h2>Halo, {{ $registrant->user->name }}</h2>
    <p>Terima kasih telah mengikuti proses seleksi penerimaan mahasiswa baru di UNMARIS.</p>
    @if ($status == 'ACCEPTED')
        <div
            style="background-color: #d1fae5; padding: 15px; border-radius: 8px; border: 1px solid #10b981; color: #065f46;">
            <h3 style="margin:0;">SELAMAT! ANDA DINYATAKAN LULUS.</h3>
        </div>
        <p>Anda diterima pada Program Studi: <strong>{{ $registrant->firstChoice->name }}</strong>.</p>
        <p>Silakan login ke portal PMB untuk melakukan <strong>Daftar Ulang</strong> dan pembayaran biaya masuk.</p>
    @else
        <div
            style="background-color: #fee2e2; padding: 15px; border-radius: 8px; border: 1px solid #ef4444; color: #991b1b;">
            <h3 style="margin:0;">MOHON MAAF</h3>
        </div>
        <p>Berdasarkan hasil seleksi, kami belum dapat menerima Anda pada periode ini. Tetap semangat dan jangan
            menyerah!</p>
    @endif

    <p>
        <a href="{{ route('pmb.status') }}"
            style="background-color: #1a237e; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            Cek Status di Portal
        </a>
    </p>

    <p style="margin-top: 30px; font-size: 12px; color: #666;">
        Email ini dikirim otomatis oleh Sistem Informasi Akademik UNMARIS.
    </p>
</body>

</html>
