<!DOCTYPE html>
<html>
<head>
    <title>KHS - {{ $student->nim }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
            border-bottom: 2px double #000; 
            padding-bottom: 10px; 
            position: relative;
        }
        .logo { width: 70px; height: auto; position: absolute; top: 0; left: 20px; }
        .kop-univ { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .kop-fakultas { font-size: 14px; font-weight: bold; text-transform: uppercase; margin-top: 2px;}
        .kop-alamat { font-size: 11px; font-style: italic; margin-top: 2px;}
        
        .judul { text-align: center; font-weight: bold; font-size: 14px; margin: 20px 0; text-decoration: underline; }
        .info-mhs { width: 100%; margin-bottom: 15px; }
        .tabel-krs { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .tabel-krs th, .tabel-krs td { border: 1px solid #000; padding: 5px; text-align: center; }
        .tabel-krs th { background-color: #f0f0f0; }
        .left { text-align: left !important; }
        .footer { width: 100%; margin-top: 40px; }
        .ttd-box { width: 35%; float: right; text-align: center; }
    </style>
</head>
<body>
     <!-- HEADER KOP SURAT DINAMIS -->
    <div class="header">
        @if($setting && $setting->logo_path)
            <img src="{{ public_path('storage/'.$setting->logo_path) }}" class="logo">
        @else
            <!-- Fallback logo default jika belum upload -->
            <img src="{{ public_path('logo.png') }}" class="logo">
        @endif

        <div class="kop-text">
            <div class="kop-univ">{{ $setting->campus_name ?? 'UNIVERSITAS STELLA MARIS SUMBA' }}</div>
            <div class="kop-fakultas">{{ $student->study_program->faculty->name ?? 'FAKULTAS SAINS DAN TEKNOLOGI' }}</div>
            <div class="kop-alamat">
                {{ $setting->campus_address ?? 'Alamat Kampus Belum Diatur' }}<br>
                Email: {{ $setting->campus_email ?? '-' }} | Web: {{ $setting->website_url ?? '-' }}
            </div>
        </div>
    </div>

    <div class="judul">KARTU HASIL STUDI (KHS)</div>

    <table class="info-mhs">
        <tr>
            <td width="15%">Nama</td><td>: {{ strtoupper($student->user->name) }}</td>
            <td width="15%">Semester</td><td>: {{ $period->name }}</td>
        </tr>
        <tr>
            <td>NIM</td><td>: {{ $student->nim }}</td>
            <td>Tahun Ajar</td><td>: {{ $period->code }}</td>
        </tr>
        <tr>
            <td>Prodi</td><td>: {{ $student->study_program->name }}</td>
            <td></td><td></td>
        </tr>
    </table>

    <table class="tabel-krs">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode MK</th>
                <th class="left">Mata Kuliah</th>
                <th width="8%">SKS</th>
                <th width="8%">Nilai</th>
                <th width="8%">Bobot</th>
                <th width="10%">SKS x N</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            @php $bobot_total = $item->classroom->course->credit_total * $item->grade_point; @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->classroom->course->code }}</td>
                <td class="left">{{ $item->classroom->course->name }}</td>
                <td>{{ $item->classroom->course->credit_total }}</td>
                <td>{{ $item->grade_letter ?? '-' }}</td>
                <td>{{ $item->grade_point ?? '0' }}</td>
                <td>{{ $bobot_total }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="right">Total</th>
                <th>{{ $total_sks }}</th>
                <th colspan="2"></th>
                <th>{{ $total_bobot }}</th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-bottom: 20px;">
        <strong>Indeks Prestasi Semester (IPS): {{ $ips }}</strong>
    </div>

    <div class="footer">
        <div class="ttd-box">
            Tambolaka, {{ $printed_at }}<br>
            Ketua Program Studi,<br><br><br><br>
            <strong>{{ $student->study_program->head_name ?? '(......................)' }}</strong><br>
            NIDN. {{ $student->study_program->head_nip ?? '................' }}
        </div>
    </div>
</body>
</html>