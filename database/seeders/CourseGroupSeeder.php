<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // WAJIB: Import Str untuk ULID
use Carbon\Carbon;

class CourseGroupSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $groups = [
            [
                'code' => 'MKK',
                'name' => 'Mata Kuliah Keilmuan',
                'description' => 'Matakuliah inti keilmuan program studi',
            ],
            [
                'code' => 'MKB',
                'name' => 'Mata Kuliah Berkarya',
                'description' => 'Matakuliah keterampilan dan praktik',
            ],
            [
                'code' => 'MPK',
                'name' => 'Mata Kuliah Pengembangan Kepribadian',
                'description' => 'Matakuliah pembentukan karakter',
            ],
            [
                'code' => 'MKU',
                'name' => 'Mata Kuliah Umum',
                'description' => 'Matakuliah wajib universitas',
            ],
        ];

        foreach ($groups as $group) {
            // 1. Cek apakah data sudah ada berdasarkan 'code'
            $existing = DB::table('course_groups')
                ->where('code', $group['code'])
                ->first();

            $payload = array_merge($group, [
                'updated_at' => $now,
            ]);

            if ($existing) {
                // 2. UPDATE: Jika ada, update datanya (ID tetap aman)
                DB::table('course_groups')
                    ->where('id', $existing->id)
                    ->update($payload);
            } else {
                // 3. INSERT: Jika baru, Generate ULID manual
                DB::table('course_groups')->insert(array_merge($payload, [
                    'id' => (string) Str::ulid(), // Generate ID disini
                    'created_at' => $now,
                ]));
            }
        }
    }
}