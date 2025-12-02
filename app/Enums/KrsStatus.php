<?php

namespace App\Enums;

enum KrsStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';

    // Helper untuk teks Bahasa Indonesia (Dipakai di View)
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Konsep',
            self::SUBMITTED => 'Menunggu Validasi',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak / Revisi',
        };
    }

    // Helper untuk warna Badge (Dipakai di View)
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',      // bg-gray-100
            self::SUBMITTED => 'yellow', // bg-yellow-100
            self::APPROVED => 'green',   // bg-green-100
            self::REJECTED => 'red',     // bg-red-100
        };
    }
}