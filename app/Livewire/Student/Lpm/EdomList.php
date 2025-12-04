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
            
            // Tidak perlu cek tanggal lagi
            
            $krs_list = StudyPlan::with(['classroom.course', 'classroom.lecturer.user'])
                ->where('student_id', $student->id)
                ->where('academic_period_id', $active_period->id)
                ->where('status', 'APPROVED')
                ->get()
                ->map(function($krs) use ($student) {
                    
                    // 1. Cek Status Pengisian
                    $has_filled = EdomResponse::where('student_id', $student->id)
                        ->where('classroom_id', $krs->classroom_id)
                        ->exists();
                    
                    $krs->edom_status = $has_filled ? 'DONE' : 'PENDING';

                    // 2. LOGIC BARU: Cek Ketersediaan Nilai
                    // Apakah dosen sudah memberi nilai huruf (A, B, C)?
                    // Jika sudah ada nilai, maka WAJIB ISI. Jika belum, Belum Bisa Isi.
                    $krs->is_grade_published = !is_null($krs->grade_letter);

                    return $krs;
                });
        }

        return view('livewire.student.lpm.edom-list', [
            'krs_list' => $krs_list,
        ])->layout('layouts.student');
    }
}