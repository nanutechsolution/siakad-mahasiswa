<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AcademicPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Konfigurasi
        $startYear = 2015;
        $currentYear = date('Y'); // Tahun saat ini (misal 2025)
        $now = Carbon::now();

        $periods = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            
            // --- 1. SEMESTER GANJIL (Kode: TAHUN + 1) ---
            // Biasanya Sept - Jan (Tahun berikutnya)
            $ganjilStart = Carbon::createFromDate($year, 9, 1); 
            $ganjilEnd = Carbon::createFromDate($year + 1, 1, 31);
            
            // Cek apakah hari ini berada di rentang semester ini
            $isGanjilActive = $now->between($ganjilStart, $ganjilEnd);

            $periods[] = [
                'code' => $year . '1', // Contoh: 20151
                'name' => 'Semester Ganjil ' . $year . '/' . ($year + 1),
                'start_date' => $ganjilStart->format('Y-m-d'),
                'end_date' => $ganjilEnd->format('Y-m-d'),
                'is_active' => $isGanjilActive,
                'allow_krs' => $isGanjilActive, // Buka KRS jika aktif
                'allow_input_score' => $isGanjilActive, // Buka Input Nilai jika aktif
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // --- 2. SEMESTER GENAP (Kode: TAHUN + 2) ---
            // Biasanya Feb - Juli (Tahun berikutnya)
            $genapStart = Carbon::createFromDate($year + 1, 2, 1);
            $genapEnd = Carbon::createFromDate($year + 1, 7, 31);

            $isGenapActive = $now->between($genapStart, $genapEnd);

            $periods[] = [
                'code' => $year . '2', // Contoh: 20152
                'name' => 'Semester Genap ' . $year . '/' . ($year + 1),
                'start_date' => $genapStart->format('Y-m-d'),
                'end_date' => $genapEnd->format('Y-m-d'),
                'is_active' => $isGenapActive,
                'allow_krs' => $isGenapActive,
                'allow_input_score' => $isGenapActive,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // --- 3. SEMESTER PENDEK (Opsional - Kode: TAHUN + 3) ---
            // Biasanya Agustus - Agustus (1 Bulan)
            /*
            $pendekStart = Carbon::createFromDate($year + 1, 8, 1);
            $pendekEnd = Carbon::createFromDate($year + 1, 8, 31);
            $isPendekActive = $now->between($pendekStart, $pendekEnd);

            $periods[] = [
                'code' => $year . '3',
                'name' => 'Semester Pendek ' . $year . '/' . ($year + 1),
                'start_date' => $pendekStart->format('Y-m-d'),
                'end_date' => $pendekEnd->format('Y-m-d'),
                'is_active' => $isPendekActive,
                'allow_krs' => $isPendekActive,
                'allow_input_score' => $isPendekActive,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            */
        }

        // Gunakan upsert agar tidak error jika dijalankan berulang (berdasarkan kolom unique 'code')
        DB::table('academic_periods')->upsert(
            $periods, 
            ['code'], 
            ['name', 'start_date', 'end_date', 'is_active', 'allow_krs', 'allow_input_score', 'updated_at']
        );
    }
}