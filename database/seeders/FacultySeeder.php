<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            ['code' => 'FST', 'name' => 'Fakultas Sains dan Teknologi', 'dean' => 'Dr. Tekno, S.T., M.T.'],
            ['code' => 'FEB', 'name' => 'Fakultas Ekonomi dan Bisnis', 'dean' => 'Dr. Eko Nomi, S.E., M.M.'],
            ['code' => 'FKIP', 'name' => 'Fakultas Keguruan dan Ilmu Pendidikan', 'dean' => 'Prof. Didik, M.Pd.'],
        ];

        foreach ($faculties as $f) {
            Faculty::firstOrCreate(['code' => $f['code']], [
                'name' => $f['name'],
                'dean_name' => $f['dean']
            ]);
        }
    }
}
