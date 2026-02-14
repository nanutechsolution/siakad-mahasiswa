<?php

namespace App\Livewire\Student;

use App\Enums\KrsStatus;
use App\Models\StudyPlanDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Transcript extends Component
{
    public function render()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return view('livewire.student.transcript', ['error' => true])->layout('layouts.student');
        }

        // 1. Ambil Semua Detail Matkul yang status headernya APPROVED (Kumulatif)
        $all_grades = StudyPlanDetail::whereHas('studyPlan', function ($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->where('status', KrsStatus::APPROVED->value);
            })
            ->with(['courseClass.course', 'studyPlan.academicPeriod'])
            ->whereNotNull('grade_point')
            ->get()
            ->sortBy('studyPlan.academicPeriod.code');

        // 2. Hitung Statistik IPK
        $total_sks = $all_grades->sum(fn($item) => $item->courseClass->course->credit_total ?? 0);
        $total_bobot = $all_grades->sum(fn($item) => ($item->courseClass->course->credit_total ?? 0) * ($item->grade_point ?? 0));
        $ipk = $total_sks > 0 ? number_format($total_bobot / $total_sks, 2) : "0.00";

        // 3. Grouping per Nama Semester
        $grouped_grades = $all_grades->groupBy('studyPlan.academicPeriod.name');

        return view('livewire.student.transcript', [
            'student' => $student,
            'grouped_grades' => $grouped_grades,
            'total_sks' => $total_sks,
            'total_bobot' => $total_bobot,
            'ipk' => $ipk
        ])->layout('layouts.student');
    }
}