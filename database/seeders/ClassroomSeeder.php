<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Classroom;
use App\Models\AcademicPeriod;
use App\Models\Lecturer;
use App\Models\Schedule;

class ClassroomSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Semester Aktif (Atau buat jika belum ada)
        $period = AcademicPeriod::where('is_active', true)->first();
        
        if (!$period) {
            $this->command->error('Error: Tidak ada Semester Aktif. Setel di database/seeder dulu.');
            return;
        }

        // 2. Ambil Data Master
        $courses = Course::where('is_active', true)->get();
        $lecturers = Lecturer::all(); // Pastikan sudah run LecturerSeeder

        if ($lecturers->isEmpty()) {
            $this->command->error('Error: Belum ada Dosen. Run LecturerSeeder dulu.');
            return;
        }

        $this->command->info('Membuka Kelas untuk ' . $courses->count() . ' Mata Kuliah...');

        foreach ($courses as $course) {
            
            // Random Dosen
            $dosen = $lecturers->random();

            // A. Buat Kelas A
            $kelas = Classroom::firstOrCreate([
                'academic_period_id' => $period->id,
                'course_id' => $course->id,
                'name' => 'A', // Kita buka Kelas A untuk semua matkul
            ], [
                'lecturer_id' => $dosen->id,
                'quota' => 40,
                'is_open' => true,
            ]);

            // B. Buat Jadwal untuk Kelas A (Jika belum ada)
            if ($kelas->schedules()->count() == 0) {
                // Random Hari & Jam
                $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
                $day = $days[array_rand($days)];
                $startHour = rand(8, 14); // Jam 8 sampai 14
                
                Schedule::create([
                    'classroom_id' => $kelas->id,
                    'day' => $day,
                    'start_time' => sprintf('%02d:00', $startHour),
                    'end_time' => sprintf('%02d:00', $startHour + 2), // Durasi 2 jam
                    'room_name' => 'R-' . rand(101, 305),
                ]);
            }
        }

        $this->command->info('Sukses! Semua mata kuliah sudah punya Kelas A.');
    }
}