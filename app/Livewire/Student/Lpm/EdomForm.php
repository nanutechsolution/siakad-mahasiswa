<?php

namespace App\Livewire\Student\Lpm;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Classroom;
use App\Models\EdomQuestion;
use App\Models\EdomResponse;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan; 

class EdomForm extends Component
{
    public $classroom;
    public $answers = []; // Array untuk menampung jawaban

    public function mount($classroomId)
    {
        $user = Auth::user();
        $student = $user->student; // Pastikan relasi student ada

        if (!$student) {
            abort(403, 'Data mahasiswa tidak ditemukan.');
        }
        
        // 1. Validasi: Pastikan mahasiswa mengambil kelas ini
        $isTaken = StudyPlan::where('student_id', $student->id)
            ->where('classroom_id', $classroomId)
            ->exists();

        if (!$isTaken) {
            abort(403, 'Anda tidak terdaftar di kelas ini.');
        }

        // 2. Validasi: Pastikan belum pernah isi
        $hasFilled = EdomResponse::where('student_id', $student->id)
            ->where('classroom_id', $classroomId)
            ->exists();

        if ($hasFilled) {
            return redirect()->route('student.edom.list')
                ->with('error', 'Anda sudah mengisi EDOM untuk kelas ini.');
        }

        // Load Data Kelas
        $this->classroom = Classroom::with(['course', 'lecturer.user'])->findOrFail($classroomId);
        
        // 3. INISIALISASI JAWABAN (PENTING AGAR TIDAK ERROR)
        // Kita siapkan slot kosong untuk setiap pertanyaan agar Livewire tidak kaget
        $questionIds = EdomQuestion::where('is_active', true)->pluck('id');
        foreach($questionIds as $id) {
            $this->answers[$id] = ''; 
        }
    }

    public function store()
    {
        // Ambil pertanyaan aktif untuk validasi
        $questions = EdomQuestion::where('is_active', true)->get();

        $rules = [];
        $messages = [];
        
        foreach ($questions as $q) {
            $rules["answers.{$q->id}"] = 'required|integer|min:1|max:5';
            $messages["answers.{$q->id}.required"] = "Poin no. {$q->sort_order} belum dinilai.";
        }

        $this->validate($rules, $messages);

        $student = Auth::user()->student;
        
        // SAFETY CHECK: Pastikan ada semester aktif
        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        if (!$activePeriod) {
            session()->flash('error', 'Gagal: Tidak ada periode akademik yang aktif.');
            return;
        }

        DB::transaction(function () use ($student, $activePeriod) {
            foreach ($this->answers as $qId => $score) {
                EdomResponse::create([
                    'academic_period_id' => $activePeriod->id,
                    'student_id' => $student->id,
                    'classroom_id' => $this->classroom->id,
                    'edom_question_id' => $qId,
                    'score' => $score
                ]);
            }
        });

        session()->flash('message', 'Terima kasih! Masukan Anda telah tersimpan.');
        return redirect()->route('student.edom.list');
    }

    public function render()
    {
        // Kirim data pertanyaan langsung di render (bukan di mount/property public)
        // Ini mencegah error "getMorphClass"
        $questions = EdomQuestion::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('livewire.student.lpm.edom-form', [
            'questions' => $questions
        ])->layout('layouts.student');
    }
}