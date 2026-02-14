<?php

namespace App\Enums;

enum KrsStatus: string
{
    // Gunakan huruf kecil agar sesuai dengan definisi di Migration (database)
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    // Helper untuk teks Bahasa Indonesia
    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Konsep',
            self::SUBMITTED => 'Menunggu Validasi',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak / Revisi',
        };
    }

    // Helper untuk warna Badge
    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'slate',
            self::SUBMITTED => 'blue',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
        };
    }
}