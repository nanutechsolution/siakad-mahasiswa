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
        $prodis = [
            ['code' => 'TI', 'name' => 'Teknik Informatika', 'degree' => 'S1'],
            ['code' => 'SI', 'name' => 'Sistem Informasi', 'degree' => 'S1'],
            ['code' => 'AG', 'name' => 'Agroteknologi', 'degree' => 'S1'],
            ['code' => 'MN', 'name' => 'Manajemen', 'degree' => 'S1'],
            ['code' => 'HK', 'name' => 'Hukum', 'degree' => 'S1'],
        ];

        foreach ($prodis as $prodi) {
            StudyProgram::create($prodi);
        }
    }
}
