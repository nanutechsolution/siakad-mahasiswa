<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EdomQuestion;

class EdomSeeder extends Seeder
{
    public function run(): void
    {
        $questions = [
            // PEDAGOGIK (Cara Mengajar)
            ['category' => 'Pedagogik', 'text' => 'Dosen menyampaikan materi perkuliahan dengan jelas dan mudah dipahami.'],
            ['category' => 'Pedagogik', 'text' => 'Dosen memberikan contoh-contoh nyata yang relevan dengan materi.'],
            ['category' => 'Pedagogik', 'text' => 'Dosen menggunakan media pembelajaran (slide, video, dll) dengan baik.'],
            
            // PROFESIONAL (Penguasaan Materi)
            ['category' => 'Profesional', 'text' => 'Dosen menguasai materi perkuliahan dengan baik.'],
            ['category' => 'Profesional', 'text' => 'Dosen mampu menjawab pertanyaan mahasiswa dengan tepat.'],
            
            // KEPRIBADIAN (Disiplin)
            ['category' => 'Kepribadian', 'text' => 'Dosen memulai dan mengakhiri perkuliahan tepat waktu.'],
            ['category' => 'Kepribadian', 'text' => 'Dosen berpakaian rapi dan sopan.'],
            ['category' => 'Kepribadian', 'text' => 'Dosen bersikap adil dan objektif terhadap mahasiswa.'],
            
            // SOSIAL (Interaksi)
            ['category' => 'Sosial', 'text' => 'Dosen mudah dihubungi untuk konsultasi akademik.'],
            ['category' => 'Sosial', 'text' => 'Dosen terbuka terhadap kritik dan saran.'],
        ];

        foreach ($questions as $index => $q) {
            EdomQuestion::firstOrCreate(['question_text' => $q['text']], [
                'category' => $q['category'],
                'sort_order' => $index + 1,
                'is_active' => true
            ]);
        }
    }
}