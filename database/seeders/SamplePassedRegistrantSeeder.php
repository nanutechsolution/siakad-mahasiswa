<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Registrant;
use App\Models\Billing;
use App\Models\Payment;
use App\Models\StudyProgram;
use App\Enums\RegistrantStatus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SamplePassedRegistrantSeeder extends Seeder
{
    
    public function run(): void
    {

        // delete existing sample users first

        // 1. Ambil Prodi Pertama (Misal Teknik Informatika)
        $prodi = StudyProgram::first();
        if (!$prodi) {
            $this->command->error('Gagal: Data Prodi kosong. Jalankan StudyProgramSeeder dulu.');
            return;
        }

        $this->command->info('Membuat 2 Contoh Camaba Lulus (1 Belum Bayar, 1 Sudah Bayar)...');

        // --- SKENARIO 1: LULUS TAPI BELUM BAYAR ---
        $user1 = User::create([
            'name' => 'Budi Lulus Belum Bayar',
            'username' => 'pmb_budi',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'role' => 'camaba',
            'email_verified_at' => now(),
        ]);

        $reg1 = Registrant::create([
            'user_id' => $user1->id,
            'registration_no' => 'PMB-2025-0001',
            'nik' => '1234567890123456',
            'school_name' => 'SMA Negeri 1 Tambolaka',
            'average_grade' => 85.50,
            'first_choice_id' => $prodi->id,
            'status' => RegistrantStatus::ACCEPTED, // Status Lulus
            'period_year' => 2025,
            'track' => 'REGULER'
        ]);

        Billing::create([
            'registrant_id' => $reg1->id,
            'title' => 'Tagihan Daftar Ulang (Skenario Belum Bayar)',
            'amount' => 5000000,
            'status' => 'UNPAID', // Masih Unpaid
            'due_date' => now()->addDays(14)
        ]);


        // --- SKENARIO 2: LULUS DAN SUDAH BAYAR (SIAP GENERATE NIM) ---
        $user2 = User::create([
            'name' => 'Siti Lulus Sudah Bayar',
            'username' => 'pmb_siti',
            'email' => 'siti@example.com',
            'password' => Hash::make('password'),
            'role' => 'camaba',
            'email_verified_at' => now(),
        ]);

        $reg2 = Registrant::create([
            'user_id' => $user2->id,
            'registration_no' => 'PMB-2025-0002',
            'nik' => '9876543210987654',
            'school_name' => 'SMK Kristen Sumba',
            'average_grade' => 90.00,
            'first_choice_id' => $prodi->id,
            'status' => RegistrantStatus::ACCEPTED, // Status Lulus
            'period_year' => 2025,
            'track' => 'REGULER'
        ]);

        $bill2 = Billing::create([
            'registrant_id' => $reg2->id,
            'title' => 'Tagihan Daftar Ulang (Skenario Sudah Bayar)',
            'amount' => 5000000,
            'status' => 'PAID', // Sudah Lunas
            'due_date' => now()->addDays(14)
        ]);

        // Tambahkan record payment agar validasi di Logic RegistrantIndex tembus
        Payment::create([
            'billing_id' => $bill2->id,
            'amount_paid' => 5000000,
            'payment_method' => 'TRANSFER',
            'payment_date' => now(),
            'status' => 'VERIFIED'
        ]);

        $this->command->info('Berhasil! Silakan cek di Dashboard Admin menu Penerimaan Maba.');
    }
}