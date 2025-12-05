<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

use function Symfony\Component\Clock\now;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jalankan Seeder Master dulu (Wajib urut)
        $this->call([
            FacultySeeder::class,
            StudyProgramSeeder::class,
            AcademicPeriodSeeder::class,
        ]);
        $this->call(UserSeeder::class);
        $this->call([
            CourseSeeder::class,    
            ClassroomSeeder::class, 
            EdomSeeder::class,
            BillingSeeder::class,
            RealLecturerSeeder::class,
            RealStudentSeeder::class,
            UserSeeder::class,
        ]);
        // \App\Models\Student::factory(20)->create();
        \App\Models\Setting::create([
            'campus_name' => 'Universitas Stella Maris Sumba',
            'campus_email' => 'info@unmaris.ac.id',
            'campus_address' => 'Tambolaka, Sumba Barat Daya',
            'campus_phone' => '0812345678',
            'website_url' => 'https://unmaris.ac.id',
        ]);
    }
}
