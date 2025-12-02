<!DOCTYPE html>
<html>

<head>
    <title>Transkrip Akademik - {{ $student->nim }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            /* Font agak kecil biar muat banyak */
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
            position: relative;
        }

        .logo {
            width: 65px;
            height: auto;
            position: absolute;
            top: 0;
            left: 10px;
        }

        .kop-text {
            margin-left: 0px;
        }

        .kop-univ {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-fakultas {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .kop-alamat {
            font-size: 10px;
            font-style: italic;
            margin-top: 2px;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 15px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }

        .info-mhs {
            width: 100%;
            margin-bottom: 15px;
            font-size: 12px;
        }

        .info-mhs td {
            padding: 2px;
            vertical-align: top;
        }

        .tabel-nilai {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .tabel-nilai th,
        .tabel-nilai td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        .tabel-nilai th {
            background-color: #f0f0f0;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        .left {
            text-align: left;
        }

        .summary-box {
            width: 40%;
            margin-top: 10px;
            border: 1px solid #000;
            padding: 5px;
            font-size: 11px;
        }

        .footer {
            width: 100%;
            margin-top: 30px;
        }

        .ttd-box {
            width: 40%;
            float: right;
            text-align: center;
        }

        /* Page Break untuk transkrip panjang */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <!-- KOP SURAT -->
    <div class="header">
        @if ($setting && $setting->logo_path)
            <img src="{{ public_path('storage/' . $setting->logo_path) }}" class="logo">
        @else
            <img src="{{ public_path('logo.png') }}" class="logo">
        @endif

        <div class="kop-text">
            <div class="kop-univ">{{ $setting->campus_name ?? 'UNIVERSITAS STELLA MARIS SUMBA' }}</div>
            <div class="kop-fakultas">{{ $student->study_program->faculty->name ?? 'FAKULTAS ...' }}</div>
            <div class="kop-alamat">{{ $setting->campus_address ?? 'Alamat Kampus' }}</div>
        </div>
    </div>

    <div class="judul">TRANSKRIP NILAI AKADEMIK SEMENTARA</div>

    <!-- INFO MAHASISWA -->
    <table class="info-mhs">
        <tr>
            <td width="15%"><strong>Nama</strong></td>
            <td width="2%">:</td>
            <td width="40%">{{ strtoupper($student->user->name) }}</td>
            <td width="15%"><strong>Program Studi</strong></td>
            <td width="2%">:</td>
            <td width="26%">{{ $student->study_program->name }}</td>
        </tr>
        <tr>
            <td><strong>NIM</strong></td>
            <td>:</td>
            <td>{{ $student->nim }}</td>
            <td><strong>Jenjang</strong></td>
            <td>:</td>
            <td>{{ $student->study_program->degree }}</td>
        </tr>
        <tr>
            <td><strong>Tempat/Tgl Lahir</strong></td>
            <td>:</td>
            <td>{{ $student->pob }}, {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d-m-Y') : '-' }}
            </td>
            <td><strong>Tanggal Cetak</strong></td>
            <td>:</td>
            <td>{{ $printed_at }}</td>
        </tr>
    </table>

    <!-- TABEL NILAI -->
    <table class="tabel-nilai">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Kode MK</th>
                <th class="left">Mata Kuliah</th>
                <th width="8%">SKS (K)</th>
                <th width="8%">Nilai (N)</th>
                <th width="8%">Bobot</th>
                <th width="10%">Mutu (KxN)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($data as $item)
                <tr>
                    <td class="center">{{ $no++ }}</td>
                    <td class="center">{{ $item->classroom->course->code }}</td>
                    <td class="left">{{ $item->classroom->course->name }}</td>
                    <td class="center">{{ $item->classroom->course->credit_total }}</td>
                    <td class="center">{{ $item->grade_letter }}</td>
                    <td class="center">{{ number_format($item->grade_point, 2) }}</td>
                    <td class="center">
                        {{ number_format($item->classroom->course->credit_total * $item->grade_point, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- RINGKASAN PRESTASI -->
    <div class="summary-box">
        <table style="width: 100%; border: none;">
            <tr>
                <td width="60%">Total SKS Ditempuh (K)</td>
                <td width="5%">:</td>
                <td class="right"><strong>{{ $total_sks }}</strong></td>
            </tr>
            <tr>
                <td>Total Nilai Mutu (M)</td>
                <td>:</td>
                <td class="right"><strong>{{ number_format($total_bobot, 2) }}</strong></td>
            </tr>
            <tr>
                <td>Indeks Prestasi Kumulatif (IPK)</td>
                <td>:</td>
                <td class="right"><strong>{{ $ipk }}</strong></td>
            </tr>
        </table>
    </div>

    <!-- TANDA TANGAN -->
    <div class="footer">
        <div class="ttd-box">
            Tambolaka, {{ $printed_at }}<br>
            a.n. Dekan<br>
            Ketua Program Studi,
            <br><br><br><br><br>
            <strong>{{ $student->study_program->head_name ?? '(................................)' }}</strong><br>
            NIDN. {{ $student->study_program->head_nip ?? '....................' }}
        </div>
        <div style="clear: both;"></div>
    </div>

</body>

</html>
