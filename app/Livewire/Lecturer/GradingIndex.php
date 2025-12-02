<?php

namespace App\Livewire\Lecturer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Classroom;
use App\Models\AcademicPeriod;

class GradingIndex extends Component
{
    public function render()
    {
        $lecturer = Auth::user()->lecturer;
        $active_period = AcademicPeriod::where('is_active', true)->first();
        
        $classes = [];
        
        if ($lecturer && $active_period) {
            $classes = Classroom::with(['course', 'study_plans'])
                ->where('lecturer_id', $lecturer->id)
                ->where('academic_period_id', $active_period->id)
                ->get()
                ->map(function($class) {
                    // Hitung progress penilaian
                    $total = $class->study_plans->count();
                    $graded = $class->study_plans->whereNotNull('grade_letter')->count();
                    $class->progress = $total > 0 ? round(($graded / $total) * 100) : 0;
                    return $class;
                });
        }

        return view('livewire.lecturer.grading-index', [
            'period' => $active_period,
            'classes' => $classes
        ])->layout('layouts.lecturer');
    }
}