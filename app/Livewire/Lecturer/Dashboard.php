<?php

namespace App\Livewire\Lecturer;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Classroom;
use App\Models\AcademicPeriod;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        //  relasi 'lecturer' di Model User
        $lecturer = $user->lecturer;

        // Ambil Semester yang sedang aktif di sistem
        $active_period = AcademicPeriod::where('is_active', true)->first();

        $classes = [];

        if ($lecturer && $active_period) {
            // Ambil kelas yang diajar dosen ini di semester aktif
            //  load 'study_plans' juga untuk menghitung jumlah mahasiswa (count)
            $classes = Classroom::with(['course', 'schedules', 'study_plans'])
                ->where('lecturer_id', $lecturer->id)
                ->where('academic_period_id', $active_period->id)
                ->get();
        }

        return view('livewire.lecturer.dashboard', [
            'lecturer' => $lecturer,
            'period' => $active_period,
            'classes' => $classes
        ])->layout('layouts.lecturer');
    }
}
