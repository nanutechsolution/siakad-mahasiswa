<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\AcademicPeriod;
use App\Models\ActivityLog;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'total_mhs' => Student::where('status', 'A')->count(),
            'total_dosen' => Lecturer::where('is_active', true)->count(),
            'total_prodi' => StudyProgram::count(),
            'semester_aktif' => AcademicPeriod::where('is_active', true)->first(),
             'recent_activities' => ActivityLog::with('user')
                                    ->latest()
                                    ->take(5)
                                    ->get()
        ])->layout('layouts.admin'); 
    }
}
