<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\StudyPlan;

class Transcript extends Component
{
    public function render()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return view('livewire.student.transcript', ['error' => true])->layout('layouts.student');
        }

        // 1. Ambil Semua Matkul yang LULUS/APPROVED (Kumulatif)
        $all_grades = StudyPlan::with(['classroom.course', 'academic_period'])
            ->where('student_id', $student->id)
            ->where('status', 'APPROVED') // Hanya nilai valid
            ->get()
            ->sortBy('academic_period.code'); // Urutkan dari semester awal

        // 2. Hitung Statistik IPK
        $total_sks = $all_grades->sum(fn($item) => $item->classroom->course->credit_total);
        
        $total_bobot = $all_grades->sum(function($item) {
            return $item->classroom->course->credit_total * $item->grade_point;
        });

        $ipk = $total_sks > 0 ? number_format($total_bobot / $total_sks, 2) : 0.00;

        // 3. Grouping per Semester (Opsional, biar rapi di view)
        $grouped_grades = $all_grades->groupBy('academic_period.name');

        return view('livewire.student.transcript', [
            'student' => $student,
            'grouped_grades' => $grouped_grades,
            'total_sks' => $total_sks,
            'total_bobot' => $total_bobot,
            'ipk' => $ipk
        ])->layout('layouts.student');
    }
}