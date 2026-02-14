<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicPeriod;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Student;
use Illuminate\Support\Str;

class KrsTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Periode Aktif
        $period = AcademicPeriod::where('is_active', true)->first();
        if (!$period) return;

        // 2. Ambil Mahasiswa (Sesuaikan NIM dengan yang Anda pakai login)
        $student = Student::first(); 
        if (!$student) return;

        // 3. Ambil/Buat Matkul di Prodi yang sama dengan Mahasiswa
        $course = Course::where('study_program_id', $student->study_program_id)->first();
        
        if ($course) {
            // 4. Buat Kelas Perkuliahan
            CourseClass::updateOrCreate(
                ['course_id' => $course->id, 'academic_period_id' => $period->id, 'name' => 'A'],
                [
                    'id' => (string) Str::ulid(),
                    'quota' => 40,
                    'is_active' => true,
                ]
            );
        }
    }
}