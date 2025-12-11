<!DOCTYPE html>
<html>
<head>
    <title>Rekap Presensi - {{ $classroom->course->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px double #000; padding-bottom: 10px; position: relative; }
        .logo { width: 60px; position: absolute; top: 0; left: 10px; }
        .title { font-size: 14px; font-weight: bold; text-transform: uppercase; margin-bottom: 2px; }
        
        .info-table { width: 100%; margin-bottom: 15px; font-size: 12px; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 4px; text-align: center; }
        .data-table th { background-color: #f0f0f0; }
        .left { text-align: left !important; }
        
        .footer { width: 100%; margin-top: 30px; }
        .ttd { float: right; width: 250px; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        @if($setting && $setting->logo_path)
            <img src="{{ public_path('storage/'.$setting->logo_path) }}" class="logo">
        @else
            <img src="{{ public_path('logo.png') }}" class="logo">
        @endif
        <div class="title">{{ $setting->campus_name ?? 'UNIVERSITAS STELLA MARIS SUMBA' }}</div>
        <div>{{ $setting->campus_address ?? 'Alamat Kampus' }}</div>
        <div style="margin-top: 10px; font-weight: bold; text-decoration: underline;">REKAPITULASI KEHADIRAN MAHASISWA</div>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%">Mata Kuliah</td>
            <td width="35%">: {{ $classroom->course->code }} - {{ $classroom->course->name }}</td>
            <td width="15%">Semester</td>
            <td width="35%">: {{ $classroom->academic_period->name }}</td>
        </tr>
        <tr>
            <td>Dosen Pengampu</td>
            <td>: {{ $lecturer->user->name }}</td>
            <td>Kelas</td>
            <td>: {{ $classroom->name }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" width="30px">No</th>
                <th rowspan="2" width="80px">NIM</th>
                <th rowspan="2" class="left" style="padding-left:5px;">Nama Mahasiswa</th>
                <th colspan="{{ $meetings->count() }}">Pertemuan Ke-</th>
                <th rowspan="2" width="40px">%</th>
            </tr>
            <tr>
                @foreach($meetings as $m)
                    <th width="20px">{{ $m->meeting_no }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($recap as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['nim'] }}</td>
                <td class="left" style="padding-left:5px;">{{ strtoupper($row['name']) }}</td>
                
                @foreach($meetings as $m)
                    @php 
                        $status = $row['attendance_data'][$m->meeting_no] ?? '-';
                        $color = $status == 'A' ? 'color:red;' : '';
                    @endphp
                    <td style="{{ $color }}">{{ $status }}</td>
                @endforeach

                <td>{{ $row['percent'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd">
            Tambolaka, {{ $printed_at }}<br>
            Dosen Pengampu,
            <br><br><br><br>
            <strong>{{ $lecturer->user->name }}</strong><br>
            NIDN. {{ $lecturer->nidn }}
        </div>
        <div style="clear: both;"></div>
    </div>

    <div style="font-size: 10px; margin-top: 20px;">
        Keterangan: H=Hadir, I=Izin, S=Sakit, A=Alpha
    </div>

</body>
</html>