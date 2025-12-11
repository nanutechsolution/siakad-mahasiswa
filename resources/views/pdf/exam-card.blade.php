<!DOCTYPE html>
<html>
<head>
    <title>Kartu Ujian - {{ $student->nim }}</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; }
        .container { border: 1px solid #000; padding: 10px; height: 95%; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 15px; position: relative; }
        .logo { width: 50px; position: absolute; top: 0; left: 0; }
        .title { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .subtitle { font-size: 10px; }
        
        .photo-box {
            width: 80px; height: 100px;
            border: 1px solid #999;
            background: #eee;
            text-align: center;
            line-height: 100px;
            float: right;
            margin-left: 10px;
            font-size: 9px;
            color: #777;
        }

        .info-table { width: 100%; margin-bottom: 15px; }
        .info-table td { padding: 2px; vertical-align: top; }
        
        .exam-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .exam-table th, .exam-table td { border: 1px solid #000; padding: 4px; text-align: left; }
        .exam-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .footer { margin-top: 20px; }
        .rules { font-size: 9px; margin-top: 20px; border: 1px dashed #666; padding: 5px; }
    </style>
</head>
<body>
    
    <!-- Kartu dibuat rangkap 2 di satu halaman A4 (Atas: Peserta, Bawah: Arsip) -->
    @for($i=1; $i<=2; $i++)
    
    <div class="container" style="height: 45%; margin-bottom: 20px;">
        <div class="header">
            @if($setting && $setting->logo_path)
                <img src="{{ public_path('storage/'.$setting->logo_path) }}" class="logo">
            @else
                <img src="{{ public_path('logo.png') }}" class="logo">
            @endif
            <div class="title">KARTU PESERTA UJIAN ({{ $i==1 ? 'MAHASISWA' : 'ARSIP PANITIA' }})</div>
            <div class="subtitle">{{ $setting->campus_name ?? 'UNMARIS' }} - Semester {{ $period->name }}</div>
        </div>

        <div class="photo-box">
            @if($student->photo)
                <img src="{{ public_path('storage/'.$student->photo) }}" style="width:100%; height:100%; object-fit:cover;">
            @else
                FOTO 3x4
            @endif
        </div>

        <table class="info-table">
            <tr><td width="80">NIM</td><td width="10">:</td><td><b>{{ $student->nim }}</b></td></tr>
            <tr><td>Nama</td><td>:</td><td>{{ strtoupper($student->user->name) }}</td></tr>
            <tr><td>Prodi</td><td>:</td><td>{{ $student->study_program->name }}</td></tr>
            <tr><td>Jenjang</td><td>:</td><td>{{ $student->study_program->degree }}</td></tr>
        </table>

        <table class="exam-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Kode</th>
                    <th>Mata Kuliah</th>
                    <th width="5%">SKS</th>
                    <th width="10%">Kelas</th>
                    <th width="15%">Paraf Pengawas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $krs)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $krs->classroom->course->code }}</td>
                    <td>{{ $krs->classroom->course->name }}</td>
                    <td style="text-align: center;">{{ $krs->classroom->course->credit_total }}</td>
                    <td style="text-align: center;">{{ $krs->classroom->name }}</td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer" style="width: 100%;">
            <div style="float: left; width: 60%; font-size: 9px;">
                <strong>Tata Tertib Ujian:</strong><br>
                1. Kartu ini wajib dibawa saat ujian berlangsung.<br>
                2. Peserta wajib berpakaian rapi (Kemeja Putih/Almamater).<br>
                3. Datang 15 menit sebelum ujian dimulai.
            </div>
            <div style="float: right; width: 30%; text-align: center;">
                <span style="font-size: 10px;">Tambolaka, {{ $printed_at }}</span><br>
                Ketua Panitia Ujian,<br><br><br>
                <strong>__________________</strong>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
    
    @if($i==1) <hr style="border-top: 1px dashed #000; margin: 20px 0;"> @endif

    @endfor

</body>
</html>