<?php

namespace App\Livewire\Student\Khs;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan;
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
            
            // A. Ambil List ID Kelas yang sudah ada nilainya (Grade Letter Not Null)
            // Hanya matkul yang sudah dinilai dosen yang wajib dievaluasi
            $graded_classes = StudyPlan::where('student_id', $student->id)
                ->where('academic_period_id', $active_period->id)
                ->where('status', 'APPROVED')
                ->whereNotNull('grade_letter') // <--- KUNCI LOGIC BARU: Hanya yg sudah ada nilai
                ->pluck('classroom_id');

            // B. Hitung berapa dari kelas tersebut yang sudah diisi EDOM-nya
            $filled_count = EdomResponse::where('student_id', $student->id)
                ->where('academic_period_id', $active_period->id)
                ->whereIn('classroom_id', $graded_classes)
                ->distinct('classroom_id')
                ->count('classroom_id');

            // C. Hutang = Total Kelas Bernilai - Total Sudah Diisi
            $this->edom_pending_count = $graded_classes->count() - $filled_count;
        }

        // 2. Load Riwayat Semester (History KHS)
        $history_periods = AcademicPeriod::whereHas('study_plans', function($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->where('status', 'APPROVED');
            })
            ->orderBy('code', 'desc')
            ->get()
            ->map(function($period) use ($student) {
                $plans = StudyPlan::with('classroom.course')
                    ->where('student_id', $student->id)
                    ->where('academic_period_id', $period->id)
                    ->where('status', 'APPROVED')
                    ->get();
                
                $sks = $plans->sum(fn($p) => $p->classroom->course->credit_total);
                $bobot = $plans->sum(fn($p) => $p->classroom->course->credit_total * $p->grade_point);
                
                $period->ips = $sks > 0 ? number_format($bobot / $sks, 2) : 0;
                $period->total_sks = $sks;
                
                return $period;
            });

        return view('livewire.student.khs.khs-index', [
            'history_periods' => $history_periods
        ])->layout('layouts.student');
    }
}