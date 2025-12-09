<?php

namespace App\Livewire\Pmb;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Registrant;
use App\Models\ExamQuestion;
use App\Models\ExamAttempt;
use Carbon\Carbon;

class CbtExam extends Component
{
    public $registrant;
    public $attempt;
    public $questions; // Semua soal
    
    // State Ujian
    public $answers = []; // Jawaban sementara [id_soal => 'A']
    public $time_remaining = 0; // Detik tersisa

    public function mount()
    {
        $this->registrant = Registrant::where('user_id', Auth::id())->first();

        if (!$this->registrant || $this->registrant->status !== \App\Enums\RegistrantStatus::VERIFIED) {
            return redirect()->route('pmb.status');
        }

        // Cek apakah sudah pernah ikut ujian?
        $this->attempt = ExamAttempt::where('registrant_id', $this->registrant->id)->first();

        if ($this->attempt && $this->attempt->status == 'FINISHED') {
            return redirect()->route('pmb.status');
        }

        // Jika belum mulai, buat attempt baru
        if (!$this->attempt) {
            $this->attempt = ExamAttempt::create([
                'registrant_id' => $this->registrant->id,
                'started_at' => now(),
                'status' => 'ONGOING'
            ]);
        }

        // Hitung sisa waktu (Misal durasi 60 menit / 3600 detik)
        $elapsed = now()->diffInSeconds($this->attempt->started_at);
        $duration = 60 * 60; // 60 Menit
        $this->time_remaining = max(0, $duration - $elapsed);

        if ($this->time_remaining <= 0) {
            $this->finishExam();
        }

        // Load Soal (Diacak)
        $this->questions = ExamQuestion::inRandomOrder()->limit(50)->get();
        
        // Load jawaban yang tersimpan (jika refresh halaman)
        if ($this->attempt->answers) {
            $this->answers = $this->attempt->answers;
        }
    }

    // Auto save setiap user klik jawaban (agar tidak hilang jika internet putus)
    public function updatedAnswers()
    {
        if ($this->attempt->status == 'ONGOING') {
            $this->attempt->update(['answers' => $this->answers]);
        }
    }

    public function finishExam()
    {
        if ($this->attempt->status == 'FINISHED') return;

        // Hitung Nilai
        $score = 0;
        $allQuestions = ExamQuestion::whereIn('id', array_keys($this->answers))->get();

        foreach ($allQuestions as $q) {
            $userAns = $this->answers[$q->id] ?? null;
            if ($userAns == $q->correct_answer) {
                $score += $q->points;
            }
        }

        // Simpan Hasil
        $this->attempt->update([
            'finished_at' => now(),
            'total_score' => $score,
            'status' => 'FINISHED',
            'answers' => $this->answers
        ]);
        
        // Update di Registrant juga (opsional, tapi memudahkan query)
        $this->registrant->update(['average_grade' => $score]); // Override nilai rapor dengan nilai ujian

        return redirect()->route('pmb.status');
    }

    public function render()
    {
        return view('livewire.pmb.cbt-exam')->layout('layouts.pmb'); // Layout polos tanpa navbar agar fokus
    }
}