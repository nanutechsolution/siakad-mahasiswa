<?php

namespace App\Livewire\Lecturer;

use Livewire\Component;
use App\Models\Classroom;
use App\Models\StudyPlan;
use Illuminate\Support\Facades\Auth;

class Grading extends Component
{
    public $classId;
    public $classroom;
    public $students = []; // Array untuk menampung data inputan

    // Aturan Validasi
    protected $rules = [
        'students.*.score_number' => 'nullable|numeric|min:0|max:100',
    ];

    public function mount($classId)
    {
        $this->classId = $classId;

        // 1. Load Kelas & Validasi Kepemilikan (Security)
        $this->classroom = Classroom::with('course', 'academic_period')
            ->where('id', $classId)
            // Pastikan yang akses adalah dosen pengampu kelas ini
            ->where('lecturer_id', Auth::user()->lecturer->id)
            ->firstOrFail();

        // 2. Load Mahasiswa (Hanya yang status KRS-nya Approved/Pending)
        $plans = StudyPlan::with('student.user')
            ->where('classroom_id', $classId)
            ->whereIn('status', ['APPROVED', 'Pending']) // Pending boleh dinilai biar sekalian approve
            ->get()
            ->sortBy(function ($plan) {
                return $plan->student->user->name; // Sort by Nama
            });

        // 3. Mapping data ke Array agar reaktif di Form
        foreach ($plans as $plan) {
            $this->students[$plan->id] = [
                'plan_id' => $plan->id,
                'nim' => $plan->student->nim,
                'name' => $plan->student->user->name,
                'score_number' => $plan->score_number, // Nilai Angka (Input)
                'grade_letter' => $plan->grade_letter, // Nilai Huruf (Output)
                'grade_point' => $plan->grade_point,   // Bobot (Output)
            ];
        }
    }

    // Fungsi Sakti: Jalan otomatis saat input berubah
    public function updatedStudents($value, $key)
    {
        // $key formatnya: {id_krs}.score_number
        $parts = explode('.', $key);
        $planId = $parts[0];
        $field = $parts[1];

        if ($field === 'score_number') {
            // 1. Hitung Konversi
            $score = (float) $value;
            $letter = $this->calculateGrade($score);
            $point = $this->calculatePoint($letter);

            // 2. Update Tampilan (UI)
            $this->students[$planId]['grade_letter'] = $letter;
            $this->students[$planId]['grade_point'] = $point;

            // 3. Simpan ke Database
            StudyPlan::where('id', $planId)->update([
                'score_number' => $score,
                'grade_letter' => $letter,
                'grade_point' => $point,
                'status' => 'APPROVED' // Otomatis setujui jika sudah dinilai
            ]);
        }
    }

    // Rumus Standar Penilaian (Bisa disesuaikan dengan aturan kampus)
    private function calculateGrade($score)
    {
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'A-';
        if ($score >= 75) return 'B+';
        if ($score >= 70) return 'B';
        if ($score >= 65) return 'B-';
        if ($score >= 60) return 'C+';
        if ($score >= 55) return 'C';
        if ($score >= 45) return 'D';
        return 'E';
    }

    private function calculatePoint($letter)
    {
        $points = [
            'A' => 4.00,
            'A-' => 3.70,
            'B+' => 3.30,
            'B' => 3.00,
            'B-' => 2.70,
            'C+' => 2.30,
            'C' => 2.00,
            'D' => 1.00,
            'E' => 0.00
        ];
        return $points[$letter] ?? 0.00;
    }

    public function render()
    {
        return view('livewire.lecturer.grading')->layout('layouts.lecturer');
    }
}
