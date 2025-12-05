<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password'); // Password sakti: "password"

        // 1. SUPER ADMIN (Tetap Buat Manual)
        User::firstOrCreate(['email' => 'admin@unmaris.ac.id'], [
            'name' => 'Super Administrator',
            'username' => 'admin',
            'role' => 'admin',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        // 2. SETUP AKUN DOSEN DEMO (AMBIL DARI DATA RIIL)
        // Kita cari satu dosen yang sudah diimport, misalnya dari Teknik Informatika (ID Prodi sesuaikan jika perlu, atau ambil sembarang)
        $dosenReal = Lecturer::with('user')->whereHas('user')->first(); 

        if ($dosenReal) {
            $this->command->info("--- INFO LOGIN DOSEN ---");
            $this->command->info("Nama     : {$dosenReal->user->name}");
            $this->command->info("Username : {$dosenReal->user->username}");
            $this->command->info("Password : password");
            $this->command->info("------------------------");
        } else {
            $this->command->warn("Data Dosen Kosong! Pastikan Anda sudah menjalankan 'RealLecturerSeeder' terlebih dahulu.");
        }

        // 3. SETUP AKUN MAHASISWA DEMO (AMBIL DARI DATA RIIL)
        // Kita cari satu mahasiswa yang sudah diimport
        $mhsReal = Student::with('user')->whereHas('user')->first();

        if ($mhsReal) {
            $this->command->info("--- INFO LOGIN MAHASISWA ---");
            $this->command->info("Nama     : {$mhsReal->user->name}");
            $this->command->info("Username : {$mhsReal->user->username}");
            $this->command->info("Password : {$mhsReal->user->username}"); // Password mhs riil diset sama dengan NIM di seeder
            $this->command->info("----------------------------");
        } else {
            $this->command->warn("Data Mahasiswa Kosong! Pastikan Anda sudah menjalankan 'RealStudentSeeder' terlebih dahulu.");
        }
    }
}