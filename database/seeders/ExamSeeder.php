<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExamQuestion;
use App\Models\ExamAttempt;
use App\Models\Registrant;
use App\Enums\RegistrantStatus;
use Illuminate\Support\Arr;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // 1. BUAT SOAL UJIAN (Hanya jika belum ada)
        if (ExamQuestion::count() == 0) {
            $this->command->info('Membuat 20 Soal Ujian Dummy...');
            
            $subjects = [
                'Matematika Dasar' => [
                    'Berapa hasil dari 15 x 12?', 
                    'Jika x + 5 = 12, berapakah x?',
                    'Akar kuadrat dari 144 adalah?',
                    'Berapa derajat sudut siku-siku?',
                    '25% dari 200 adalah?'
                ],
                'Bahasa Inggris' => [
                    'What is the past tense of "eat"?',
                    'She ___ to the market yesterday.',
                    'Choose the synonym of "Happy".',
                    'Which one is a vegetable?',
                    'Complete: The book is ___ the table.'
                ],
                'Logika Umum' => [
                    'Semua ayam berkokok. Jago adalah ayam. Maka?',
                    'Lanjutkan deret: 2, 4, 8, 16, ...',
                    'Jakarta adalah ibukota Indonesia. Tokyo adalah ibukota?',
                    'Air : Haus = Makanan : ...',
                    'Jika kemarin hari Jumat, maka besok adalah hari?'
                ],
                'Wawasan Kebangsaan' => [
                    'Siapakah presiden pertama RI?',
                    'Tanggal berapa Indonesia merdeka?',
                    'Apa lambang negara Indonesia?',
                    'Bhinneka Tunggal Ika artinya?',
                    'Ibukota Jawa Barat adalah?'
                ]
            ];
            
            foreach ($subjects as $category => $questionsList) {
                foreach ($questionsList as $qText) {
                    ExamQuestion::create([
                        'question_text' => $qText,
                        'option_a' => 'Pilihan Jawaban A',
                        'option_b' => 'Pilihan Jawaban B',
                        'option_c' => 'Pilihan Jawaban C (Benar)', // Kita set C selalu benar biar logic seedernya gampang
                        'option_d' => 'Pilihan Jawaban D',
                        'correct_answer' => 'C',
                        'points' => 5 // 20 soal x 5 poin = 100
                    ]);
                }
            }
        }

        // 2. SIMULASI PESERTA UJIAN
        $questions = ExamQuestion::all();
        
        // Ambil pendaftar acak (yang belum Accepted/Rejected)
        // Kita paksa ubah status mereka jadi VERIFIED agar logis kalau mereka ikut ujian
        $registrants = Registrant::whereIn('status', [RegistrantStatus::DRAFT, RegistrantStatus::SUBMITTED, RegistrantStatus::VERIFIED])
            ->inRandomOrder()
            ->take(20)
            ->get();

        if ($registrants->isEmpty()) {
            $this->command->warn('Tidak ada data pendaftar untuk disimulasikan ujiannya.');
            return;
        }

        $this->command->info('Mensimulasikan ujian untuk ' . $registrants->count() . ' pendaftar...');

        foreach ($registrants as $reg) {
            // A. Update status jadi VERIFIED (Syarat ikut ujian)
            $reg->update(['status' => RegistrantStatus::VERIFIED]);

            // B. Tentukan Nasib (Pintar / Biasa)
            // 60% Kemungkinan Pintar (Nilai Tinggi), 40% Nilai Rendah
            $isSmart = rand(1, 100) <= 60; 

            $answers = [];
            $totalScore = 0;

            foreach ($questions as $q) {
                // Jika Pintar, 90% jawab benar (C). Jika tidak, 30% jawab benar.
                $chance = $isSmart ? 90 : 30;
                $isCorrect = rand(1, 100) <= $chance;

                if ($isCorrect) {
                    $ans = 'C'; // Jawaban Benar
                    $totalScore += $q->points;
                } else {
                    $ans = Arr::random(['A', 'B', 'D']); // Jawaban Salah
                }

                $answers[$q->id] = $ans;
            }

            // C. Simpan Hasil Ujian (ExamAttempt)
            ExamAttempt::create([
                'registrant_id' => $reg->id,
                'started_at' => now()->subMinutes(60), // Mulai 1 jam lalu
                'finished_at' => now()->subMinutes(rand(1, 20)), // Selesai barusan
                'total_score' => $totalScore,
                'status' => 'FINISHED',
                'answers' => $answers
            ]);

            // D. Update Nilai di Tabel Registrant (Agar muncul di tabel seleksi tanpa join)
            // Kita override nilai rapor dengan nilai ujian
            $reg->update(['average_grade' => $totalScore]);

            $this->command->info("Peserta {$reg->registration_no} ({$reg->user->name}) selesai ujian. Skor: $totalScore");
        }
    }
}