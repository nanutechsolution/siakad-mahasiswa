<?php

namespace App\Livewire\Student\Lpm;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan;
use App\Models\EdomResponse;

class EdomList extends Component
{
    public function render()
    {
        $student = Auth::user()->student;
        $active_period = AcademicPeriod::where('is_active', true)->first();
        
        $krs_list = collect();

        if ($student && $active_period) {
            // Ambil KRS yang sudah disetujui (APPROVED)
            $krs_list = StudyPlan::with(['classroom.course', 'classroom.lecturer.user'])
                ->where('student_id', $student->id)
                ->where('academic_period_id', $active_period->id)
                ->where('status', 'APPROVED')
                ->get()
                ->map(function($krs) use ($student, $active_period) {
                    // Cek apakah sudah mengisi EDOM untuk kelas ini?
                    // Kita cek apakah ada setidaknya 1 record response
                    $has_filled = EdomResponse::where('student_id', $student->id)
                        ->where('classroom_id', $krs->classroom_id)
                        ->exists();
                    
                    $krs->edom_status = $has_filled ? 'DONE' : 'PENDING';
                    return $krs;
                });
        }

        return view('livewire.student.lpm.edom-list', [
            'krs_list' => $krs_list
        ])->layout('layouts.student');
    }
}