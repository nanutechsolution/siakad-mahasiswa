<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password'); // Password sakti: "password"

        // 1. SUPER ADMIN
        User::firstOrCreate(['email' => 'admin@unmaris.ac.id'], [
            'name' => 'Super Administrator',
            'username' => 'admin',
            'role' => 'admin',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        // 2. DOSEN TETAP (Untuk Simulasi Buka Kelas & Input Nilai)
        $prodiTI = StudyProgram::where('code', 'TI')->first();
        
        $dosenUser = User::firstOrCreate(['email' => 'dosen@unmaris.ac.id'], [
            'name' => 'Budi Santoso, M.Kom.',
            'username' => '00112233', // NIDN
            'role' => 'lecturer',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        Lecturer::firstOrCreate(['user_id' => $dosenUser->id], [
            'study_program_id' => $prodiTI->id ?? 1,
            'nidn' => '00112233',
            'nip_internal' => '19900101',
            'front_title' => '',
            'back_title' => 'M.Kom.',
            'is_active' => true,
        ]);

        // 3. MAHASISWA CONTOH (Untuk Simulasi KRS)
        $mhsUser = User::firstOrCreate(['email' => 'mhs@unmaris.ac.id'], [
            'name' => 'Frederique Jenkins', // Nama Keren
            'username' => '24TI0001', // NIM
            'role' => 'student',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        Student::firstOrCreate(['user_id' => $mhsUser->id], [
            'study_program_id' => $prodiTI->id ?? 1,
            'nim' => '24TI0001',
            'entry_year' => '2024',
            'pob' => 'Sumba',
            'dob' => '2005-08-17',
            'gender' => 'L',
            'status' => 'A',
        ]);
    }
}