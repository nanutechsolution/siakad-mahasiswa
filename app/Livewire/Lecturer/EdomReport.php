<?php

namespace App\Livewire\Lecturer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicPeriod;
use App\Models\Classroom;
use App\Models\EdomQuestion;

class EdomReport extends Component
{
    public function render()
    {
        $lecturer = Auth::user()->lecturer;
        $active_period = AcademicPeriod::where('is_active', true)->first();
        
        $report = collect();
        $overall_score = 0;

        if ($lecturer && $active_period) {
            // Ambil ID kelas yang diajar dosen ini semester ini
            $classIds = Classroom::where('lecturer_id', $lecturer->id)
                ->where('academic_period_id', $active_period->id)
                ->pluck('id');

            // Query Agregat Rata-rata per Pertanyaan
            $report = EdomQuestion::with(['edom_responses' => function($q) use ($classIds) {
                    $q->whereIn('classroom_id', $classIds);
                }])
                ->get()
                ->map(function($q) use ($classIds) {
                    // Hitung rata-rata skor untuk pertanyaan ini di kelas-kelas dosen tsb
                    $avg = $q->edom_responses->whereIn('classroom_id', $classIds)->avg('score');
                    $q->average_score = $avg ? number_format($avg, 2) : 0;
                    return $q;
                })
                ->groupBy('category');

            // Hitung Skor Keseluruhan
            // (Total Skor Semua Respon / Jumlah Respon)
            $allResponses = \App\Models\EdomResponse::whereIn('classroom_id', $classIds);
            $overall_score = $allResponses->avg('score');
        }

        return view('livewire.lecturer.edom-report', [
            'report' => $report,
            'overall_score' => $overall_score ? number_format($overall_score, 2) : 0,
            'period' => $active_period
        ])->layout('layouts.lecturer');
    }
}