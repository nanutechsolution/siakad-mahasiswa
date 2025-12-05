<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;

class FacultySeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            ['code' => 'FST',  'name' => 'Fakultas Teknik',                    'dean_name' => 'Dr. Tekno, S.T., M.T.'],
            ['code' => 'FKES', 'name' => 'Fakultas Kesehatan',                 'dean_name' => 'Dr. Eko Nomi, S.E., M.M.'],
            ['code' => 'FKIP', 'name' => 'Fakultas Keguruan dan Ilmu Pendidikan', 'dean_name' => 'Prof. Didik, M.Pd.'],
            ['code' => 'FEB',  'name' => 'Fakultas Ekonomi dan Bisnis',        'dean_name' => 'Prof. Didik, M.Pd.'],
        ];

        foreach ($faculties as $f) {
            Faculty::updateOrCreate(
                ['code' => $f['code']],
                ['name' => $f['name'], 'dean_name' => $f['dean_name']]
            );
        }
    }
}
