<!DOCTYPE html>
<html>
<head>
    <title>Kartu Peserta PMB - {{ $registrant->registration_no }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; }
        .container { width: 100%; border: 2px solid #333; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; position: relative; }
        .logo { width: 60px; position: absolute; top: 0; left: 10px; }
        .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .subtitle { font-size: 12px; margin-top: 5px; }
        
        .photo-box {
            width: 120px;
            height: 150px;
            border: 1px solid #999;
            background: #f0f0f0;
            text-align: center;
            line-height: 150px;
            float: right;
            margin-left: 20px;
            color: #999;
        }
        
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 6px 0; vertical-align: top; }
        .label { width: 140px; font-weight: bold; }
        .colon { width: 10px; }
        
        .prodi-box {
            margin-top: 20px;
            border: 1px dashed #666;
            padding: 10px;
            background: #f9f9f9;
        }
        
        .footer { margin-top: 30px; font-size: 10px; text-align: center; color: #666; border-top: 1px solid #ccc; padding-top: 10px; }
        
        .big-no {
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 10px;
            display: block;
        }

        /* Simulasi QR Code */
        .qr-simulation {
            width: 80px;
            height: 80px;
            background: #333;
            margin-top: 10px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
             @if($setting && $setting->logo_path)
                <img src="{{ public_path('storage/'.$setting->logo_path) }}" class="logo">
            @else
                <img src="{{ public_path('logo.png') }}" class="logo">
            @endif
            
            <div class="title">KARTU TANDA PESERTA</div>
            <div class="title" style="font-size: 14px; margin-top: 5px;">PENERIMAAN MAHASISWA BARU TAHUN {{ $registrant->period_year }}</div>
            <div class="subtitle">{{ $setting->campus_name ?? 'UNIVERSITAS STELLA MARIS SUMBA' }}</div>
        </div>

        <!-- Pas Foto -->
        <div class="photo-box">
            @if(isset($registrant->documents['foto'])) 
                {{-- Jika ada fitur upload foto profil di PMB nanti --}}
                <img src="{{ public_path('storage/'.$registrant->documents['foto']) }}" style="width:100%; height:100%; object-fit:cover;">
            @else
                PAS FOTO
                3 x 4
            @endif
        </div>

        <table class="info-table">
            <tr>
                <td class="label">NOMOR PESERTA</td>
                <td class="colon">:</td>
                <td><span class="big-no">{{ $registrant->registration_no }}</span></td>
            </tr>
            <tr>
                <td class="label">NAMA LENGKAP</td>
                <td class="colon">:</td>
                <td style="text-transform: uppercase; font-weight: bold;">{{ $user->name }}</td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td class="colon">:</td>
                <td>{{ $registrant->nik }}</td>
            </tr>
            <tr>
                <td class="label">ASAL SEKOLAH</td>
                <td class="colon">:</td>
                <td>{{ $registrant->school_name }}</td>
            </tr>
            <tr>
                <td class="label">JALUR MASUK</td>
                <td class="colon">:</td>
                <td>{{ $registrant->track }}</td>
            </tr>
        </table>

        <div class="prodi-box">
            <table class="info-table">
                <tr>
                    <td class="label" style="width: 130px;">Pilihan 1</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $registrant->firstChoice->name }} ({{ $registrant->firstChoice->degree }})</strong></td>
                </tr>
                @if($registrant->secondChoice)
                <tr>
                    <td class="label">Pilihan 2</td>
                    <td class="colon">:</td>
                    <td>{{ $registrant->secondChoice->name }} ({{ $registrant->secondChoice->degree }})</td>
                </tr>
                @endif
            </table>
        </div>

        <div style="margin-top: 20px;">
            <strong>Pernyataan:</strong><br>
            <i style="font-size: 11px;">
                Saya menyatakan bahwa data yang saya isikan dalam formulir pendaftaran ini adalah benar. 
                Saya bersedia menerima sanksi pembatalan kelulusan apabila dikemudian hari ditemukan ketidaksesuaian data.
            </i>
        </div>
        
        <br>
        
        <!-- Area TTD -->
        <table width="100%" style="margin-top: 10px;">
            <tr>
                <td width="50%" align="center">
                    <div class="qr-simulation">
                        QR CODE<br>VALIDASI
                    </div>
                </td>
                <td width="50%" align="center">
                    Tambolaka, {{ date('d F Y') }}<br>
                    Peserta,
                    <br><br><br><br>
                    <strong>({{ strtoupper($user->name) }})</strong>
                </td>
            </tr>
        </table>

        <div class="footer">
            Dicetak secara otomatis oleh Sistem Informasi Akademik UNMARIS pada {{ $printed_at }}.<br>
            Harap membawa kartu ini saat pelaksanaan Ujian Seleksi atau Daftar Ulang.
        </div>
    </div>

</body>
</html>