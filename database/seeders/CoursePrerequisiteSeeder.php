<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursePrerequisiteSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil kurikulum aktif
        $curriculum = DB::table('curriculums')
            ->where('is_active', 1)
            ->first();

        if (!$curriculum) {
            $this->command->warn('❌ Kurikulum aktif tidak ditemukan');
            return;
        }

        /**
         * Helper ambil curriculum_course_id
         * Berdasarkan nama + semester
         */
        $getCC = function (string $nameLike, int $semester) use ($curriculum) {
            return DB::table('curriculum_courses as cc')
                ->join('courses as c', 'c.id', '=', 'cc.course_id')
                ->where('cc.curriculum_id', $curriculum->id)
                ->where('cc.semester', $semester)
                ->where('c.name', 'LIKE', "%{$nameLike}%")
                ->value('cc.id');
        };

        /**
         * MATKUL ← PRASYARAT
         * [matkul, smt, prasyarat, smt]
         */
        $prerequisites = [
            ['Algoritma',      1, 'Logika',        1],
            ['Struktur Data',  2, 'Algoritma',     1],
            ['Basis Data',     2, 'Algoritma',     1],
        ];

        foreach ($prerequisites as [$course, $smt, $prereq, $prSmt]) {

            $courseCCId = $getCC($course, $smt);
            $prereqCCId = $getCC($prereq, $prSmt);

            if (!$courseCCId || !$prereqCCId) {
                $this->command->warn(
                    "⚠️ Skip: {$course} (smt {$smt}) ← {$prereq} (smt {$prSmt})"
                );
                continue;
            }

            DB::table('course_prerequisites')->updateOrInsert(
                [
                    'curriculum_course_id'              => $courseCCId,
                    'prerequisite_curriculum_course_id' => $prereqCCId,
                ],
                [
                    'min_grade'  => 'D',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            $this->command->info("✔ {$course} ← {$prereq}");
        }

        $this->command->info(
            "✅ Seeder prasyarat OK (Kurikulum: {$curriculum->name} {$curriculum->year})"
        );
    }
}
