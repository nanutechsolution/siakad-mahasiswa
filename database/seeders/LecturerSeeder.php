<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Hash;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        // Cek apakah Prodi sudah ada
        if (StudyProgram::count() == 0) {
            $this->command->error('HARAP ISI DATA PRODI TERLEBIH DAHULU!');
            return;
        }

        $password = Hash::make('password'); // Password default dosen: password

        $this->command->info('Membuat 10 Dosen Dummy...');

        // Kita buat 10 Dosen
        for ($i = 1; $i <= 10; $i++) {
            
            // 1. Buat User Dosen
            $name = fake()->name();
            // Generate email simpel: dosen1@unmaris.ac.id
            $email = 'dosen' . $i . '@unmaris.ac.id'; 
            // Generate NIDN dummy
            $nidn = '00' . str_pad($i, 6, '0', STR_PAD_LEFT); // Contoh: 00000001

            $user = User::create([
                'name' => $name,
                'username' => $nidn, // Login pakai NIDN
                'email' => $email,
                'role' => 'lecturer', // Role penting!
                'password' => $password,
                'email_verified_at' => now(),
            ]);

            // 2. Buat Data Lecturer
            // Ambil Prodi Acak
            $prodi = StudyProgram::inRandomOrder()->first();

            Lecturer::create([
                'user_id' => $user->id,
                'study_program_id' => $prodi->id,
                'nidn' => $nidn,
                'nip_internal' => '1990' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'front_title' => fake()->randomElement(['', 'Dr.', 'Ir.']),
                'back_title' => fake()->randomElement(['S.Kom., M.T.', 'S.T., M.Kom', 'M.Cs']),
                'phone' => fake()->phoneNumber(),
                'is_active' => true,
            ]);
        }

        $this->command->info('Berhasil membuat 10 Dosen!');
    }
}