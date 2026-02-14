<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // PENTING: Import ini untuk ULID

class CurriculumCourseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Data Parent (Fail early jika tidak ada)
        $curriculum = DB::table('curriculums')->where('is_active', true)->first();
        $courseGroup = DB::table('course_groups')->where('code', 'MKK')->first();

        if (!$curriculum || !$courseGroup) {
            $this->command->error('❌ Kurikulum Aktif atau Group MKK tidak ditemukan. Seeder dibatalkan.');
            return;
        }

        // 2. Definisi Data (Dataset)
        // Format: [Nama Persis, Semester, Total SKS, Teori, Praktek]
        $coursesData = [
            ['Logika',          1, 3, 3, 0],
            ['Algoritma',       1, 3, 2, 1],
            ['Struktur Data',   2, 3, 2, 1],
            ['Basis Data',      2, 3, 2, 1],
        ];

        $this->command->info("🚀 Memulai mapping ke Kurikulum: {$curriculum->name}...");

        foreach ($coursesData as [$exactName, $semester, $total, $theory, $practice]) {
            
            // A. Cari Course (Gunakan Exact Match '=' agar akurat)
            $course = DB::table('courses')
                ->where('name', $exactName) // JANGAN pakai LIKE
                ->first();

            if (!$course) {
                $this->command->warn("⚠️  Skip: Matkul '{$exactName}' belum ada di master courses.");
                continue;
            }

            // B. Cek apakah relasi sudah ada?
            $existingRelation = DB::table('curriculum_courses')
                ->where('curriculum_id', $curriculum->id)
                ->where('course_id', $course->id)
                ->first();

            // Data yang akan disimpan (Payload)
            $payload = [
                'semester'        => $semester,
                'course_group_id' => $courseGroup->id,
                'is_mandatory'    => true,
                'credit_total'    => $total,
                'credit_theory'   => $theory,
                'credit_practice' => $practice,
                'updated_at'      => now(),
            ];

            if ($existingRelation) {
                // UPDATE: Jika sudah ada, update datanya saja (JANGAN sentuh ID)
                DB::table('curriculum_courses')
                    ->where('id', $existingRelation->id)
                    ->update($payload);
                
                $this->command->line("   Refreshed: {$exactName}");
            } else {
                // CREATE: Jika belum ada, bikin ID baru (ULID)
                DB::table('curriculum_courses')->insert(array_merge($payload, [
                    'id'            => (string) Str::ulid(), // GENERATE ULID DISINI
                    'curriculum_id' => $curriculum->id,
                    'course_id'     => $course->id,
                    'created_at'    => now(),
                ]));

                $this->command->info("   Mapped: {$exactName}");
            }
        }
    }
}