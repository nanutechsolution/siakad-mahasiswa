<?php

namespace App\Livewire\Student\Khs;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan;

class KhsIndex extends Component
{
    public function render()
    {
        $student = Auth::user()->student;

        // Cari semester mana saja mahasiswa ini punya data nilai (KHS)
        // Kita group by semester biar tidak duplikat
        $history_periods = AcademicPeriod::whereHas('study_plans', function($q) use ($student) {
                $q->where('student_id', $student->id)
                  ->where('status', 'APPROVED'); // Hanya tampilkan semester yang sudah ada nilai Approved
            })
            ->orderBy('code', 'desc')
            ->get()
            ->map(function($period) use ($student) {
                // Hitung IPS sekalian untuk preview di tabel
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