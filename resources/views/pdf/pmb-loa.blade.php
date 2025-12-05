<!DOCTYPE html>
<html>
<head>
    <title>Letter of Acceptance - {{ $registrant->registration_no }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 30px; position: relative; }
        .logo { width: 80px; position: absolute; top: 0; left: 0; }
        .univ-name { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .univ-address { font-size: 12px; font-style: italic; }
        
        .content { margin: 0 40px; }
        .title { text-align: center; font-weight: bold; font-size: 16px; text-decoration: underline; margin-bottom: 5px; }
        .nomor { text-align: center; margin-bottom: 30px; }
        
        .table-info { width: 100%; margin-left: 20px; margin-bottom: 20px; }
        .table-info td { padding: 3px; vertical-align: top; }
        
        .box-prodi {
            border: 1px solid #000;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
            font-size: 14px;
            background-color: #f9f9f9;
        }
        
        .footer { margin-top: 50px; text-align: right; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-top: 60px; }
    </style>
</head>
<body>

    <div class="header">
        @if($setting && $setting->logo_path)
            <img src="{{ public_path('storage/'.$setting->logo_path) }}" class="logo">
        @else
            <img src="{{ public_path('logo.png') }}" class="logo">
        @endif
        <div class="univ-name">{{ $setting->campus_name ?? 'UNIVERSITAS STELLA MARIS SUMBA' }}</div>
        <div class="univ-address">{{ $setting->campus_address ?? 'Alamat Kampus' }}</div>
        <div class="univ-address">Website: {{ $setting->website_url }} | Email: {{ $setting->campus_email }}</div>
    </div>

    <div class="content">
        <div class="title">SURAT KETERANGAN LULUS SELEKSI</div>
        <div class="nomor">Nomor: {{ $registrant->registration_no }}/PMB/{{ $registrant->period_year }}</div>

        <p>Panitia Penerimaan Mahasiswa Baru (PMB) {{ $setting->campus_name ?? 'UNMARIS' }} Tahun Akademik {{ $registrant->period_year }}/{{ $registrant->period_year + 1 }}, menerangkan bahwa:</p>

        <table class="table-info">
            <tr>
                <td width="30%">Nama Lengkap</td>
                <td width="5%">:</td>
                <td><strong>{{ strtoupper($user->name) }}</strong></td>
            </tr>
            <tr>
                <td>Nomor Pendaftaran</td>
                <td>:</td>
                <td>{{ $registrant->registration_no }}</td>
            </tr>
            <tr>
                <td>Asal Sekolah</td>
                <td>:</td>
                <td>{{ $registrant->school_name }}</td>
            </tr>
        </table>

        <p>Berdasarkan hasil seleksi administrasi dan akademik yang telah dilakukan, peserta tersebut dinyatakan:</p>

        <div class="box-prodi">
            LULUS / DITERIMA<br>
            <span style="font-weight: normal; font-size: 12px;">Pada Program Studi:</span><br>
            {{ strtoupper($registrant->firstChoice->name) }} ({{ $registrant->firstChoice->degree }})
        </div>

        <p>Kepada peserta yang dinyatakan lulus, diwajibkan untuk segera melakukan <strong>Daftar Ulang</strong> dan penyelesaian administrasi keuangan sesuai jadwal yang telah ditentukan.</p>

        <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>

        <div class="footer">
            <div>Tambolaka, {{ date('d F Y') }}</div>
            <div>Ketua Panitia PMB,</div>
            
            <!-- TTD SCAN (Optional) -->
            <!-- <img src="ttd.png" width="100"> -->
            
            <div class="ttd-name">Dr. Ketua Panitia, M.Pd.</div>
            <div>NIDN. 0011223344</div>
        </div>
    </div>

</body>
</html>