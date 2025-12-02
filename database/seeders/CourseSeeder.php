<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\StudyProgram;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DATA MATA KULIAH UMUM (MKU) - Wajib Nasional
        // Group: MKU, Wajib: YA
        $mku = [
            ['name' => 'Pendidikan Agama', 'smt' => 1, 'sks' => 2],
            ['name' => 'Pancasila', 'smt' => 1, 'sks' => 2],
            ['name' => 'Kewarganegaraan', 'smt' => 2, 'sks' => 2],
            ['name' => 'Bahasa Indonesia', 'smt' => 2, 'sks' => 2],
            ['name' => 'Bahasa Inggris', 'smt' => 1, 'sks' => 2],
            ['name' => 'Kewirausahaan', 'smt' => 5, 'sks' => 2, 'group' => 'MPK'], // MPK = Pengembangan Kepribadian
            ['name' => 'Kuliah Kerja Nyata (KKN)', 'smt' => 7, 'sks' => 4, 'group' => 'MBB'], // MBB = Berkehidupan Bermasyarakat
        ];

        // 2. DATA PRODI TEKNIK INFORMATIKA (TI)
        $ti_courses = [
            // Semester 1
            ['name' => 'Algoritma & Pemrograman', 'smt' => 1, 'sks' => 3, 'group' => 'MKK', 'wajib' => true],
            ['name' => 'Matematika Diskrit', 'smt' => 1, 'sks' => 3, 'group' => 'MKK', 'wajib' => true],
            ['name' => 'Pengantar TI', 'smt' => 1, 'sks' => 2, 'group' => 'MKK', 'wajib' => true],
            
            // Semester 2
            ['name' => 'Struktur Data', 'smt' => 2, 'sks' => 3, 'group' => 'MKK', 'wajib' => true],
            ['name' => 'Basis Data 1', 'smt' => 2, 'sks' => 3, 'group' => 'MKK', 'wajib' => true],
            ['name' => 'Sistem Operasi', 'smt' => 2, 'sks' => 3, 'group' => 'MKK', 'wajib' => true],

            // Semester 3
            ['name' => 'Pemrograman Web 1', 'smt' => 3, 'sks' => 3, 'group' => 'MKB', 'wajib' => true], // MKB = Berkarya
            ['name' => 'Jaringan Komputer', 'smt' => 3, 'sks' => 3, 'group' => 'MKB', 'wajib' => true],
            ['name' => 'Basis Data Lanjut', 'smt' => 3, 'sks' => 3, 'group' => 'MKK', 'wajib' => true],

            // Semester 4
            ['name' => 'Pemrograman Web 2 (Framework)', 'smt' => 4, 'sks' => 3, 'group' => 'MKB', 'wajib' => true],
            ['name' => 'Rekayasa Perangkat Lunak', 'smt' => 4, 'sks' => 3, 'group' => 'MKB', 'wajib' => true],
            ['name' => 'Kecerdasan Buatan', 'smt' => 4, 'sks' => 3, 'group' => 'MKK', 'wajib' => true],

            // Semester 5 (Mulai Ada Pilihan)
            ['name' => 'Pemrograman Mobile', 'smt' => 5, 'sks' => 3, 'group' => 'MKB', 'wajib' => true],
            ['name' => 'Data Mining', 'smt' => 5, 'sks' => 3, 'group' => 'MKB', 'wajib' => false], // Pilihan
            ['name' => 'Cloud Computing', 'smt' => 5, 'sks' => 3, 'group' => 'MKB', 'wajib' => false], // Pilihan

            // Semester 6
            ['name' => 'Metodologi Penelitian', 'smt' => 6, 'sks' => 2, 'group' => 'MPB', 'wajib' => true],
            ['name' => 'Kerja Praktek (KP)', 'smt' => 6, 'sks' => 2, 'group' => 'MBB', 'wajib' => true],
            ['name' => 'Internet of Things (IoT)', 'smt' => 6, 'sks' => 3, 'group' => 'MKB', 'wajib' => false], // Pilihan

            // Semester 8
            ['name' => 'Skripsi / Tugas Akhir', 'smt' => 8, 'sks' => 6, 'group' => 'MBB', 'wajib' => true],
        ];

        // 3. EKSEKUSI KE DATABASE
        $prodis = StudyProgram::all();

        foreach ($prodis as $prodi) {
            
            // A. Masukkan MKU (Mata Kuliah Umum) ke semua Prodi
            foreach ($mku as $idx => $mk) {
                $kode = 'UN-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT) . '-' . $prodi->code;
                
                Course::updateOrCreate(['code' => $kode], [
                    'study_program_id' => $prodi->id,
                    'name' => $mk['name'],
                    'name_en' => null,
                    'semester_default' => $mk['smt'],
                    'credit_total' => $mk['sks'],
                    'credit_theory' => $mk['sks'], 
                    'credit_practice' => 0,
                    'group_code' => $mk['group'] ?? 'MKU', // Default MKU
                    'is_mandatory' => true, // MKU pasti wajib
                    'is_active' => true,
                ]);
            }

            // B. Masukkan Matkul Prodi (Khusus TI, yg lain default aja biar ga error)
            // Jika Anda mau menambahkan data SI, buat array $si_courses dan cek if($prodi->code == 'SI')
            $targetCourses = ($prodi->code == 'TI') ? $ti_courses : $this->getDefaultCourses();

            foreach ($targetCourses as $idx => $mk) {
                // Format Kode: TI-101 (Prodi-Smt-NoUrut)
                $kode = $prodi->code . '-' . $mk['smt'] . str_pad($idx + 1, 2, '0', STR_PAD_LEFT);

                Course::updateOrCreate(['code' => $kode], [
                    'study_program_id' => $prodi->id,
                    'name' => $mk['name'],
                    'semester_default' => $mk['smt'],
                    'credit_total' => $mk['sks'],
                    'credit_theory' => $mk['sks'] > 1 ? $mk['sks'] - 1 : 1, // Logika kasar SKS Teori
                    'credit_practice' => 1, // Asumsi ada praktek 1 SKS
                    'group_code' => $mk['group'] ?? 'MKK',
                    'is_mandatory' => $mk['wajib'] ?? true,
                    'is_active' => true,
                ]);
            }
        }
    }

    // Data Dummy untuk prodi selain TI
    private function getDefaultCourses()
    {
        return [
            ['name' => 'Pengantar Keilmuan', 'smt' => 1, 'sks' => 3, 'group' => 'MKK', 'wajib' => true],
            ['name' => 'Teori Dasar 1', 'smt' => 2, 'sks' => 3, 'group' => 'MKK', 'wajib' => true],
            ['name' => 'Praktikum Lanjut', 'smt' => 3, 'sks' => 3, 'group' => 'MKB', 'wajib' => true],
            ['name' => 'Manajemen Proyek', 'smt' => 4, 'sks' => 2, 'group' => 'MKB', 'wajib' => true],
            ['name' => 'Skripsi', 'smt' => 8, 'sks' => 6, 'group' => 'MBB', 'wajib' => true],
        ];
    }
}