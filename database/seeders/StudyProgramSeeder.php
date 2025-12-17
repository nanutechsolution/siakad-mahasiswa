<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        // DATA PRODI RESMI UNMARIS (SESUAI DIKTI)
        $prodis = [
            // Fakultas Teknik
            ['code' => 'TI', 'name' => 'Teknik Informatika', 'degree' => 'S1'],
            ['code' => 'TL', 'name' => 'Teknik Lingkungan', 'degree' => 'S1'],
            
            // Fakultas Ekonomi & Bisnis
            ['code' => 'MI', 'name' => 'Manajemen Informatika', 'degree' => 'D3'],
            ['code' => 'BD', 'name' => 'Bisnis Digital', 'degree' => 'S1'],
            
            // Fakultas Kesehatan
            ['code' => 'ARS', 'name' => 'Administrasi Rumah Sakit', 'degree' => 'S1'],
            ['code' => 'K3', 'name' => 'Keselamatan dan Kesehatan Kerja', 'degree' => 'S1'],
            
            // Fakultas Keguruan
            ['code' => 'PTI', 'name' => 'Pendidikan Teknologi Informasi', 'degree' => 'S1'],
        ];

        foreach ($prodis as $prodi) {
            // Gunakan updateOrInsert agar tidak duplikat jika dijalankan berkali-kali
            DB::table('study_programs')->updateOrInsert(
                ['name' => $prodi['name'], 'degree' => $prodi['degree']], // Kunci pencarian
                [
                    'code' => $prodi['code'],
                    'is_package' => 0,
                    'total_credits' => $prodi['degree'] == 'D3' ? 110 : 144, // SKS Default
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}