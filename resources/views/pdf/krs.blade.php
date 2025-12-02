<!DOCTYPE html>
<html>

<head>
    <title>KRS - {{ $student->nim }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
            position: relative;
        }

        .logo {
            width: 70px;
            height: auto;
            position: absolute;
            top: 0;
            left: 20px;
        }

        .kop-text {
            margin-left: 0px;
        }

        .kop-univ {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-fakultas {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .kop-alamat {
            font-size: 11px;
            font-style: italic;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 20px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .info-mhs {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-mhs td {
            padding: 2px;
            vertical-align: top;
        }

        .tabel-krs {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .tabel-krs th,
        .tabel-krs td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
        }

        .tabel-krs th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .footer {
            width: 100%;
            margin-top: 40px;
        }

        .ttd-box {
            width: 35%;
            float: right;
            text-align: center;
        }

        .ttd-doswal {
            width: 35%;
            float: left;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- HEADER KOP SURAT DINAMIS -->
    <div class="header">
        @if ($setting && $setting->logo_path)
            <img src="{{ public_path('storage/' . $setting->logo_path) }}" class="logo">
        @else
            <!-- Fallback logo default jika belum upload -->
            <img src="{{ public_path('logo.png') }}" class="logo">
        @endif

        <div class="kop-text">
            <div class="kop-univ">{{ $setting->campus_name ?? 'UNIVERSITAS STELLA MARIS SUMBA' }}</div>
            <div class="kop-fakultas">{{ $student->study_program->faculty->name ?? 'FAKULTAS SAINS DAN TEKNOLOGI' }}
            </div>
            <div class="kop-alamat">
                {{ $setting->campus_address ?? 'Alamat Kampus Belum Diatur' }}<br>
                Email: {{ $setting->campus_email ?? '-' }} | Web: {{ $setting->website_url ?? '-' }}
            </div>
        </div>
    </div>

    <div class="judul">KARTU RENCANA STUDI (KRS)</div>

    <table class="info-mhs">
        <tr>
            <td width="15%"><strong>NIM</strong></td>
            <td width="2%">:</td>
            <td width="35%">{{ $student->nim }}</td>
            <td width="15%"><strong>Semester</strong></td>
            <td width="2%">:</td>
            <td width="31%">{{ $period->name }} ({{ $period->code }})</td>
        </tr>
        <tr>
            <td><strong>Nama</strong></td>
            <td>:</td>
            <td>{{ strtoupper($student->user->name) }}</td>
            <td><strong>Tahun Ajaran</strong></td>
            <td>:</td>
            <td>{{ substr($period->code, 0, 4) }}/{{ substr($period->code, 0, 4) + 1 }}</td>
        </tr>
        <tr>
            <td><strong>Program Studi</strong></td>
            <td>:</td>
            <td>{{ $student->study_program->name }}</td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>:</td>
            <td>{{ $printed_at }}</td>
        </tr>
    </table>

    <table class="tabel-krs">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode MK</th>
                <th>Mata Kuliah</th>
                <th width="10%">Kelas</th>
                <th width="8%">SKS</th>
                <th>Jadwal & Ruang</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->classroom->course->code }}</td>
                    <td>{{ $item->classroom->course->name }}</td>
                    <td class="center">{{ $item->classroom->name }}</td>
                    <td class="center">{{ $item->classroom->course->credit_total }}</td>
                    <td>
                        @foreach ($item->classroom->schedules as $s)
                            <div>{{ $s->day }}, {{ \Carbon\Carbon::parse($s->start_time)->format('H:i') }}
                                ({{ $s->room_name }})</div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="right">Total SKS Yang Diambil</th>
                <th class="center">{{ $total_sks }}</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="ttd-doswal">
            Menyetujui,<br>
            Dosen Wali
            <br><br><br><br><br>
            <!-- NAMA OTOMATIS -->
            <strong>{{ $student->academic_advisor->user->name ?? '_______________________' }}</strong><br>
            NIDN. {{ $student->academic_advisor->nidn ?? '.....................' }}
        </div>

        <div class="ttd-box">
            Tambolaka, {{ date('d F Y') }}<br>
            Mahasiswa Ybs,
            <br><br><br><br><br>
            <strong>{{ strtoupper($student->user->name) }}</strong><br>
            NIM. {{ $student->nim }}
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>
