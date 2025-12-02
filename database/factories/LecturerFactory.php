<?php
namespace Database\Factories;

use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LecturerFactory extends Factory
{
    public function definition(): array
    {
        // Ambil Prodi secara acak
        $prodi = StudyProgram::inRandomOrder()->first();

        // Gelar-gelar umum di IT/Kampus
        $frontTitles = [null, 'Dr.', 'Ir.', 'Prof. Dr.'];
        $backTitles = ['S.Kom., M.Kom.', 'S.T., M.T.', 'S.Si., M.Cs.', 'S.Kom., M.T.', 'Ph.D'];

        return [
            // User akan kita override di Seeder agar lebih rapi
            'user_id' => User::factory(), 
            
            'study_program_id' => $prodi->id,
            'nidn' => $this->faker->unique()->numerify('0#######'), // 8 digit NIDN
            'nip_internal' => $this->faker->unique()->numerify('19##########'), // Format NIP
            'front_title' => $this->faker->randomElement($frontTitles),
            'back_title' => $this->faker->randomElement($backTitles),
            'phone' => $this->faker->phoneNumber(),
            'is_active' => 1,
        ];
    }
}