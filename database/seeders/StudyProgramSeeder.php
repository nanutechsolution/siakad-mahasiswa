<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // WAJIB: Import ini
use Carbon\Carbon;
use App\Models\Faculty;

class StudyProgramSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        
        // Ambil Fakultas pertama untuk relasi (Opsional: sesuaikan logika Anda)
        $faculty = Faculty::first(); 
        $facultyId = $faculty ? $faculty->id : null;

        $programs = [
            [
                'code' => 'TI',
                'name' => 'Teknik Informatika',
                'degree' => 'S1',
                'is_package' => false,
                'total_credits' => 144,
            ],
            [
                'code' => 'SI',
                'name' => 'Sistem Informasi',
                'degree' => 'S1',
                'is_package' => false,
                'total_credits' => 144,
            ],
            [
                'code' => 'MI',
                'name' => 'Manajemen Informatika',
                'degree' => 'D3',
                'is_package' => true,
                'total_credits' => 110,
            ],
        ];

        foreach ($programs as $prog) {
            // 1. Cek data existing by Code
            $existing = DB::table('study_programs')->where('code', $prog['code'])->first();

            $payload = array_merge($prog, [
                'faculty_id' => $facultyId,
                'updated_at' => $now,
            ]);

            if ($existing) {
                // UPDATE: ID jangan disentuh
                DB::table('study_programs')
                    ->where('id', $existing->id)
                    ->update($payload);
            } else {
                // INSERT: Generate ULID manual disini!
                DB::table('study_programs')->insert(array_merge($payload, [
                    'id' => (string) Str::ulid(),
                    'created_at' => $now,
                ]));
            }
        }
    }
}