<!DOCTYPE html>
<html>
<head>
    <title>Surat Keterangan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 30px; position: relative; }
        .logo { width: 80px; position: absolute; top: 0; left: 0; }
        .univ-name { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .univ-address { font-size: 12px; font-style: italic; }
        
        .content { margin: 0 40px; }
        .title { text-align: center; font-weight: bold; font-size: 16px; text-decoration: underline; margin-bottom: 5px; text-transform: uppercase; }
        .nomor { text-align: center; margin-bottom: 30px; }
        
        .table-info { width: 100%; margin-left: 20px; margin-bottom: 20px; }
        .table-info td { padding: 4px; vertical-align: top; }
        
        .footer { margin-top: 50px; text-align: right; }
        .ttd-name { font-weight: bold; text-decoration: underline; margin-top: 70px; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
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
        <div class="title">SURAT KETERANGAN {{ str_replace('_', ' ', $request->type) }}</div>
        <div class="nomor">Nomor: {{ $request->letter_number }}</div>

        <p>Yang bertanda tangan di bawah ini:</p>

        <table class="table-info">
            <tr>
                <td width="30%">Nama</td>
                <td width="5%">:</td>
                <!-- Menggunakan data Kaprodi atau Pejabat Berwenang -->
                <td><strong>{{ $student->study_program->head_name ?? '...........................' }}</strong></td>
            </tr>
            <tr>
                <td>NIDN</td>
                <td>:</td>
                <td>{{ $student->study_program->head_nip ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Ketua Program Studi {{ $student->study_program->name }}</td>
            </tr>
        </table>

        <p>Dengan ini menerangkan bahwa:</p>

        <table class="table-info">
            <tr>
                <td width="30%">Nama Mahasiswa</td>
                <td width="5%">:</td>
                <td><strong>{{ strtoupper($user->name) }}</strong></td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $student->nim }}</td>
            </tr>
            <tr>
                <td>Tempat/Tgl Lahir</td>
                <td>:</td>
                <td>{{ $student->pob }}, {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->translatedFormat('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td>Program Studi</td>
                <td>:</td>
                <td>{{ $student->study_program->name }} ({{ $student->study_program->degree }})</td>
            </tr>
            <tr>
                <td>Semester</td>
                <td>:</td>
                <td>{{ $period->name ?? 'Ganjil' }} Tahun Akademik {{ substr($period->code ?? date('Y'), 0, 4) }}/{{ substr($period->code ?? date('Y'), 0, 4) + 1 }}</td>
            </tr>
        </table>

        <p class="text-justify">
            Surat keterangan ini diberikan kepada yang bersangkutan untuk keperluan: <strong>{{ $request->purpose }}</strong>.
        </p>

        <p>
            Demikian surat keterangan ini dibuat dengan sesungguhnya untuk dapat dipergunakan sebagaimana mestinya.
        </p>

        <div class="footer">
            <div>Tambolaka, {{ $date }}</div>
            <div>Ketua Program Studi,</div>
            
            <!-- Space TTD -->
            <div class="ttd-name">{{ $student->study_program->head_name ?? '(Nama Kaprodi)' }}</div>
            <div>NIDN. {{ $student->study_program->head_nip ?? '..........' }}</div>
        </div>
    </div>

</body>
</html>