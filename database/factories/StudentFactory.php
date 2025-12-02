<?php

namespace Database\Factories;

use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        // 1. Ambil Prodi secara acak
        $prodi = StudyProgram::inRandomOrder()->first();

        // 2. Generate NIM (Format: 24 + KodeProdi + 4 Angka Acak)
        // Contoh: 24TI1092
        $year = '24';
        $nim = $year . $prodi->code . $this->faker->unique()->numerify('####');

        return [
            // Logic Senior: Factory Student otomatis bikin User
            'user_id' => User::factory()->create([
                'name' => $this->faker->name(),
                'username' => $nim, // Username pakai NIM
                'email' => $this->faker->unique()->safeEmail(),
                'password' => bcrypt('12345678'), // Password default semua user
                'role' => 'student',
            ])->id,

            'study_program_id' => $prodi->id,
            'nim' => $nim,
            'entry_year' => '20' . $year,
            'pob' => $this->faker->city(),
            'dob' => $this->faker->date('Y-m-d', '2005-01-01'), // Umur sktr 20 thn
            'phone' => $this->faker->phoneNumber(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'status' => 'A',
        ];
    }
}
