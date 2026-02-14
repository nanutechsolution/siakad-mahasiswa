<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\StudyProgram;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $prodis = StudyProgram::all();

        /**
         * =========================
         * MATA KULIAH UMUM (MKU)
         * =========================
         */
        $mku = [
            ['name' => 'Pendidikan Agama', 'short' => 'AGM', 'smt' => 1, 'sks' => 2],
            ['name' => 'Pancasila', 'short' => 'PAN', 'smt' => 1, 'sks' => 2],
            ['name' => 'Kewarganegaraan', 'short' => 'KWN', 'smt' => 2, 'sks' => 2],
            ['name' => 'Bahasa Indonesia', 'short' => 'BIN', 'smt' => 2, 'sks' => 2],
            ['name' => 'Bahasa Inggris', 'short' => 'BING', 'smt' => 1, 'sks' => 2],
            ['name' => 'Kewirausahaan', 'short' => 'KWU', 'smt' => 5, 'sks' => 2],
            ['name' => 'Kuliah Kerja Nyata', 'short' => 'KKN', 'smt' => 7, 'sks' => 4],
        ];

        /**
         * =========================
         * TEKNIK INFORMATIKA (TI)
         * =========================
         */
        $tiCourses = [
            // Semester 1
            ['name' => 'Algoritma & Pemrograman', 'short' => 'ALG', 'smt' => 1, 'sks' => 3],
            ['name' => 'Matematika Diskrit', 'short' => 'MD', 'smt' => 1, 'sks' => 3],
            ['name' => 'Pengantar Teknologi Informasi', 'short' => 'PTI', 'smt' => 1, 'sks' => 2],

            // Semester 2
            ['name' => 'Struktur Data', 'short' => 'STRDAT', 'smt' => 2, 'sks' => 3],
            ['name' => 'Basis Data 1', 'short' => 'BD1', 'smt' => 2, 'sks' => 3],
            ['name' => 'Sistem Operasi', 'short' => 'SO', 'smt' => 2, 'sks' => 3],

            // Semester 3
            ['name' => 'Pemrograman Web 1', 'short' => 'WEB1', 'smt' => 3, 'sks' => 3],
            ['name' => 'Jaringan Komputer', 'short' => 'JARKOM', 'smt' => 3, 'sks' => 3],
            ['name' => 'Basis Data Lanjut', 'short' => 'BD2', 'smt' => 3, 'sks' => 3],

            // Semester 4
            ['name' => 'Pemrograman Web 2', 'short' => 'WEB2', 'smt' => 4, 'sks' => 3],
            ['name' => 'Rekayasa Perangkat Lunak', 'short' => 'RPL', 'smt' => 4, 'sks' => 3],
            ['name' => 'Kecerdasan Buatan', 'short' => 'AI', 'smt' => 4, 'sks' => 3],

            // Semester 5
            ['name' => 'Pemrograman Mobile', 'short' => 'MOB', 'smt' => 5, 'sks' => 3],
            ['name' => 'Data Mining', 'short' => 'DM', 'smt' => 5, 'sks' => 3],
            ['name' => 'Cloud Computing', 'short' => 'CLOUD', 'smt' => 5, 'sks' => 3],

            // Semester 6
            ['name' => 'Metodologi Penelitian', 'short' => 'METPEN', 'smt' => 6, 'sks' => 2],
            ['name' => 'Kerja Praktek', 'short' => 'KP', 'smt' => 6, 'sks' => 2],
            ['name' => 'Internet of Things', 'short' => 'IOT', 'smt' => 6, 'sks' => 3],

            // Semester 8
            ['name' => 'Skripsi', 'short' => 'SKRIPSI', 'smt' => 8, 'sks' => 6],
        ];

        foreach ($prodis as $prodi) {

            /**
             * A. MKU untuk semua prodi
             */
            foreach ($mku as $i => $mk) {
                Course::updateOrCreate(
                    [
                        'code' => 'UN-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT) . '-' . $prodi->code,
                    ],
                    [
                        'study_program_id' => $prodi->id,
                        'short_code' => $mk['short'],
                        'name' => $mk['name'],
                        'semester_default' => $mk['smt'],
                        'credit_total' => $mk['sks'],
                        'credit_theory' => $mk['sks'],
                        'credit_practice' => 0,
                        'group_code' => 'MKU',
                        'is_mandatory' => true,
                        'is_active' => true,
                    ]
                );
            }

            /**
             * B. Matkul Prodi
             */
            $courses = ($prodi->code === 'TI')
                ? $tiCourses
                : $this->defaultCourses();

            foreach ($courses as $i => $mk) {
                Course::updateOrCreate(
                    [
                        'code' => $prodi->code . '-' . $mk['smt'] . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                    ],
                    [
                        'study_program_id' => $prodi->id,
                        'short_code' => $mk['short'],
                        'name' => $mk['name'],
                        'semester_default' => $mk['smt'],
                        'credit_total' => $mk['sks'],
                        'credit_theory' => max($mk['sks'] - 1, 1),
                        'credit_practice' => $mk['sks'] > 1 ? 1 : 0,
                        'group_code' => 'MKK',
                        'is_mandatory' => true,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function defaultCourses(): array
    {
        return [
            ['name' => 'Pengantar Keilmuan', 'short' => 'PK', 'smt' => 1, 'sks' => 3],
            ['name' => 'Teori Dasar', 'short' => 'TD', 'smt' => 2, 'sks' => 3],
            ['name' => 'Praktikum Lanjut', 'short' => 'PL', 'smt' => 3, 'sks' => 3],
            ['name' => 'Manajemen Proyek', 'short' => 'MP', 'smt' => 4, 'sks' => 2],
            ['name' => 'Skripsi', 'short' => 'SKR', 'smt' => 8, 'sks' => 6],
        ];
    }
}
