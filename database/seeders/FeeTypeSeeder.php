<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeeType;
use Illuminate\Support\Facades\DB;

class FeeTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('fee_types')->delete();

        $types = [
            // ===== PMB =====
            ['code' => 'PMB_FORMULIR', 'name' => 'Biaya Formulir Pendaftaran PMB'],

            // ===== Wajib Akademik =====
            ['code' => 'SPP', 'name' => 'Sumbangan Pembinaan Pendidikan (SPP)'],
            ['code' => 'GEDUNG', 'name' => 'Uang Pangkal / Pembangunan'],
            ['code' => 'REGISTRASI', 'name' => 'Biaya Registrasi / Daftar Ulang'],
            ['code' => 'CUTI', 'name' => 'Biaya Cuti Akademik'],
            ['code' => 'AKTIFASI', 'name' => 'Biaya Aktivasi Status Mahasiswa'],

            // ===== Kegiatan Akademik =====
            ['code' => 'PRAKTIKUM', 'name' => 'Biaya Praktikum'],
            ['code' => 'KKN', 'name' => 'Biaya Kuliah Kerja Nyata'],
            ['code' => 'PPL', 'name' => 'Biaya Praktik Pengalaman Lapangan'],
            ['code' => 'MAGANG', 'name' => 'Biaya Magang / MBKM'],

            // ===== Ujian & Kelulusan =====
            ['code' => 'UTS', 'name' => 'Biaya Ujian Tengah Semester'],
            ['code' => 'UAS', 'name' => 'Biaya Ujian Akhir Semester'],
            ['code' => 'SKRIPSI', 'name' => 'Biaya Skripsi'],
            ['code' => 'PROPOSAL', 'name' => 'Biaya Seminar Proposal'],
            ['code' => 'SIDANG', 'name' => 'Biaya Sidang Akhir'],
            ['code' => 'YUDISIUM', 'name' => 'Biaya Yudisium'],
            ['code' => 'WISUDA', 'name' => 'Biaya Wisuda'],

            // ===== Administrasi =====
            ['code' => 'KTM', 'name' => 'Biaya Pembuatan KTM'],
            ['code' => 'ALMAMATER', 'name' => 'Biaya Jas Almamater'],
            ['code' => 'TRANSKRIP', 'name' => 'Biaya Cetak Transkrip Nilai'],
            ['code' => 'IJAZAH', 'name' => 'Biaya Cetak Ijazah'],
            ['code' => 'SURAT', 'name' => 'Biaya Surat Akademik'],

            // ===== Denda & Lain-lain =====
            ['code' => 'DENDA', 'name' => 'Denda Administrasi'],
            ['code' => 'TERLAMBAT', 'name' => 'Denda Keterlambatan Pembayaran'],
            ['code' => 'LAINNYA', 'name' => 'Biaya Lain-lain'],
        ];

        foreach ($types as $type) {
            FeeType::firstOrCreate(
                ['code' => $type['code']],
                ['name' => $type['name']]
            );
        }
    }
}
