<?php

namespace Database\Seeders;

use App\Models\StudyProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudyProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Daftar Prodi disesuaikan dengan Data CSV (Laporan Penugasan Dosen)
        $prodis = [
            // DATA REAL DARI CSV
            ['code' => 'TI', 'name' => 'Teknik Informatika', 'degree' => 'S1'],
            ['code' => 'MI', 'name' => 'Manajemen Informatika', 'degree' => 'D3'],
            ['code' => 'TL', 'name' => 'Teknik Lingkungan', 'degree' => 'S1'],

            // PRODI TAMBAHAN (Opsional/Dummy - bisa dihapus jika tidak perlu)
            ['code' => 'SI', 'name' => 'Sistem Informasi', 'degree' => 'S1'],
            ['code' => 'AG', 'name' => 'Agroteknologi', 'degree' => 'S1'],
            ['code' => 'MN', 'name' => 'Manajemen', 'degree' => 'S1'],
            ['code' => 'HK', 'name' => 'Hukum', 'degree' => 'S1'],
        ];

        foreach ($prodis as $prodi) {
            // Gunakan firstOrCreate agar aman dijalankan berkali-kali (mencegah duplikat)
            StudyProgram::firstOrCreate(
                ['code' => $prodi['code']], 
                $prodi
            );
        }
    }
}