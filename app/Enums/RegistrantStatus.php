<?php

namespace App\Enums;

enum RegistrantStatus: string
{
    case DRAFT = 'DRAFT';         // Sedang mengisi form
    case SUBMITTED = 'SUBMITTED'; // Sudah finalisasi, menunggu admin
    case VERIFIED = 'VERIFIED';   // Berkas valid, lanjut ujian (jika ada)
    case ACCEPTED = 'ACCEPTED';   // Lulus seleksi
    case REJECTED = 'REJECTED';   // Tidak lulus

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'slate',
            self::SUBMITTED => 'yellow',
            self::VERIFIED => 'blue',
            self::ACCEPTED => 'green',
            self::REJECTED => 'red',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draf',
            self::SUBMITTED => 'Menunggu Verifikasi',
            self::VERIFIED => 'Terverifikasi',
            self::ACCEPTED => 'Diterima',
            self::REJECTED => 'Ditolak',
        };
    }
}
