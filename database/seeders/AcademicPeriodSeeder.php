<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // WAJIB: Import ini agar bisa generate ULID
use Carbon\Carbon;

class AcademicPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $startYear = 2015;
        $currentYear = 2026; 
        $now = Carbon::now();

        $periods = [];

        for ($year = $startYear; $year <= $currentYear; $year++) {
            
            // --- 1. SEMESTER GANJIL ---
            $ganjilStart = Carbon::createFromDate($year, 9, 1); 
            $ganjilEnd = Carbon::createFromDate($year + 1, 1, 31);
            $isGanjilActive = $now->between($ganjilStart, $ganjilEnd);

            $periods[] = [
                'id' => (string) Str::ulid(), // <--- WAJIB ADA
                'code' => $year . '1',
                'name' => 'Semester Ganjil ' . $year . '/' . ($year + 1),
                'start_date' => $ganjilStart->format('Y-m-d'),
                'end_date' => $ganjilEnd->format('Y-m-d'),
                'is_active' => $isGanjilActive,
                'allow_krs' => $isGanjilActive,
                'allow_input_score' => $isGanjilActive,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // --- 2. SEMESTER GENAP ---
            $genapStart = Carbon::createFromDate($year + 1, 2, 1);
            $genapEnd = Carbon::createFromDate($year + 1, 7, 31);
            $isGenapActive = $now->between($genapStart, $genapEnd);

            $periods[] = [
                'id' => (string) Str::ulid(), // <--- WAJIB ADA
                'code' => $year . '2',
                'name' => 'Semester Genap ' . $year . '/' . ($year + 1),
                'start_date' => $genapStart->format('Y-m-d'),
                'end_date' => $genapEnd->format('Y-m-d'),
                'is_active' => $isGenapActive,
                'allow_krs' => $isGenapActive,
                'allow_input_score' => $isGenapActive,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Upsert: Jika 'code' sudah ada, update kolom lain. Jika belum, insert baru (pakai ID di atas).
        DB::table('academic_periods')->upsert(
            $periods, 
            ['code'], 
            ['name', 'start_date', 'end_date', 'is_active', 'allow_krs', 'allow_input_score', 'updated_at']
        );
    }
}