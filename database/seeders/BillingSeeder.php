<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Billing;
use App\Models\Student;
use App\Models\AcademicPeriod;
use App\Models\Payment;

class BillingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Semester Aktif
        $period = AcademicPeriod::where('is_active', true)->first();

        if (!$period) {
            $this->command->error('Error: Tidak ada periode aktif. Jalankan AcademicPeriodSeeder dulu.');
            return;
        }

        // 2. Ambil Semua Mahasiswa
        $students = Student::where('status', 'A')->get();

        if ($students->isEmpty()) {
            $this->command->error('Error: Tidak ada data mahasiswa. Jalankan StudentSeeder dulu.');
            return;
        }

        $this->command->info('Membuat Tagihan SPP untuk ' . $students->count() . ' Mahasiswa...');

        foreach ($students as $student) {
            
            // Tentukan Status secara Acak (70% Lunas, 30% Belum)
            // Agar simulasi terlihat nyata
            $isPaid = rand(1, 100) <= 70; 
            $status = $isPaid ? 'PAID' : 'UNPAID';
            $amount = 3000000; // Rp 3.000.000

            // A. Buat Tagihan (Billing)
            $billing = Billing::create([
                'student_id' => $student->id,
                'academic_period_id' => $period->id,
                'title' => 'SPP Semester ' . $period->name,
                'description' => 'Pembayaran SPP Tetap & Variabel Tahun Ajaran ' . $period->code,
                'amount' => $amount,
                'due_date' => now()->addMonth(), // Jatuh tempo bulan depan
                'status' => $status,
            ]);

            // B. Jika status PAID, buatkan data Pembayarannya juga
            if ($isPaid) {
                Payment::create([
                    'billing_id' => $billing->id,
                    'amount_paid' => $amount,
                    'payment_method' => 'TRANSFER',
                    'payment_date' => now()->subDays(rand(1, 10)), // Bayar beberapa hari lalu
                    'status' => 'VERIFIED', // Langsung dianggap sah
                    'verified_by' => null, // Bisa diisi ID admin jika mau
                ]);
            }
        }

        $this->command->info('Sukses! Tagihan SPP telah dibuat.');
    }
}