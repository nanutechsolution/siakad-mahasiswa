<?php

namespace App\Livewire\Student\Khs;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail; // Import Model Detail
use App\Models\EdomResponse;

class KhsIndex extends Component
{
    public $edom_pending_count = 0;

    public function render()
    {
        $student = Auth::user()->student;
        $active_period = AcademicPeriod::where('is_active', true)->first();

        if ($student && $active_period) {
            
            // 1. Cari Kelas yang SUDAH DINILAI tapi BELUM DI-EDOM
            
            // Ambil List ID Kelas (course_class_id) dari tabel DETAIL
            $graded_classes = StudyPlanDetail::whereHas('studyPlan', function ($q) use ($student, $active_period) {
                    $q->where('student_id', $student->id)
                      ->where('academic_period_id', $active_period->id)
                      ->where('status', 'approved');
                })
                ->whereNotNull('grade_letter') 
                ->pluck('course_class_id');

            // Hitung EDOM (Sesuaikan kolom classroom_id -> course_class_id jika tabel edom juga di-refactor)
            $filled_count = EdomResponse::where('student_id', $student->id)
                ->where('academic_period_id', $active_period->id)
                ->whereIn('course_class_id', $graded_classes)
                ->distinct('course_class_id')
                ->count('course_class_id');

            $this->edom_pending_count = $graded_classes->count() - $filled_count;
        }

        // 2. Load Riwayat Semester (History KHS)
        $history_periods = AcademicPeriod::whereHas('study_plans', function($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->where('status', 'approved');
            })
            ->orderBy('code', 'desc')
            ->get()
            ->map(function($period) use ($student) {
                // Ambil details melalui header study_plans
                $details = StudyPlanDetail::whereHas('studyPlan', function($q) use ($student, $period) {
                        $q->where('student_id', $student->id)
                          ->where('academic_period_id', $period->id)
                          ->where('status', 'approved');
                    })
                    ->with('courseClass.course')
                    ->get();
                
                $sks = $details->sum(fn($d) => $d->courseClass->course->credit_total ?? 0);
                $bobot = $details->sum(fn($d) => ($d->courseClass->course->credit_total ?? 0) * ($d->grade_point ?? 0));
                
                $period->ips = $sks > 0 ? number_format($bobot / $sks, 2) : 0;
                $period->total_sks = $sks;
                
                return $period;
            });

        return view('livewire.student.khs.khs-index', [
            'history_periods' => $history_periods
        ])->layout('layouts.student');
    }
}