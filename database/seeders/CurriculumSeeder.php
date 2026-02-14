<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // PENTING: Import Str untuk generate ULID
use Carbon\Carbon;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Ambil ID Prodi secara dinamis (JANGAN Hardcode '1')
        // Kita ambil prodi pertama yang ditemukan, atau cari spesifik 'Teknik Informatika'
        $prodi = DB::table('study_programs')->first();

        if (!$prodi) {
            $this->command->error("❌ Tidak ada data di tabel 'study_programs'. Seeder dibatalkan.");
            return;
        }

        $this->command->info("ℹ️ Menambahkan kurikulum untuk prodi: {$prodi->name}");

        // 2. Dataset Kurikulum
        $curriculums = [
            [
                'name'      => 'Kurikulum OBE 2020',
                'year'      => 2020,
                'is_active' => false,
            ],
            [
                'name'      => 'Kurikulum MBKM 2022',
                'year'      => 2022,
                'is_active' => false,
            ],
            [
                'name'      => 'Kurikulum OBE 2024',
                'year'      => 2024,
                'is_active' => true,
            ],
        ];

        foreach ($curriculums as $data) {
            // A. Cek apakah kurikulum sudah ada (Berdasarkan Prodi & Tahun)
            $existing = DB::table('curriculums')
                ->where('study_program_id', $prodi->id)
                ->where('year', $data['year'])
                ->first();

            $payload = [
                'study_program_id' => $prodi->id,
                'name'             => $data['name'],
                'year'             => $data['year'],
                'is_active'        => $data['is_active'],
                'updated_at'       => $now,
            ];

            if ($existing) {
                // UPDATE: Jika ada, update datanya saja (ID Tetap)
                DB::table('curriculums')
                    ->where('id', $existing->id)
                    ->update($payload);
                
                $this->command->line("   Refreshed: {$data['name']}");
            } else {
                // INSERT: Jika baru, Generate ULID baru
                DB::table('curriculums')->insert(array_merge($payload, [
                    'id'         => (string) Str::ulid(), // GENERATE ULID DISINI
                    'created_at' => $now,
                ]));
                
                $this->command->info("   Created: {$data['name']}");
            }
        }
    }
}