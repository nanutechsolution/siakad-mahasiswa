<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicPeriod::create([
            'code' => '20241',
            'name' => 'Ganjil 2024/2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-02-01',
            'is_active' => true,
            'allow_krs' => true,
            'allow_input_score' => false,
        ]);
    }
}
