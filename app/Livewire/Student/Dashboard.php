<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\StudyPlan;
use App\Models\AcademicPeriod;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $student = $user->student;

        $active_period = AcademicPeriod::where('is_active', true)->first();

        $total_sks = 0;
        $krs_aktif = collect();
        $jadwal_hari_ini = collect();
        $greeting = $this->getGreeting();

        if ($student && $active_period) {
            $krs_aktif = StudyPlan::with(['classroom.course', 'classroom.schedules', 'classroom.lecturer.user'])
                ->where('student_id', $student->id)
                ->where('academic_period_id', $active_period->id)
                ->get();

            $total_sks = $krs_aktif->sum(fn($k) => $k->classroom->course->credit_total);

            // Filter Jadwal Hari Ini & Urutkan Jam
            $hari_indo = $this->getHariIndonesia(Carbon::now()->format('l'));

            $jadwal_hari_ini = $krs_aktif->flatMap(function ($krs) {
                return $krs->classroom->schedules->map(function ($sch) use ($krs) {
                    $sch->course_name = $krs->classroom->course->name;
                    $sch->class_name = $krs->classroom->name;
                    $sch->lecturer_name = $krs->classroom->lecturer->user->name ?? '-';
                    return $sch;
                });
            })->filter(function ($sch) use ($hari_indo) {
                return $sch->day == $hari_indo;
            })->sortBy('start_time');
        }

        return view('livewire.student.dashboard', [
            'student' => $student,
            'active_period' => $active_period,
            'krs_aktif' => $krs_aktif,
            'total_sks' => $total_sks,
            'jadwal_hari_ini' => $jadwal_hari_ini,
            'greeting' => $greeting
        ])->layout('layouts.student');
    }

    private function getGreeting()
    {
        $hour = Carbon::now()->hour;
        if ($hour < 12) return 'Selamat Pagi';
        if ($hour < 15) return 'Selamat Siang';
        if ($hour < 18) return 'Selamat Sore';
        return 'Selamat Malam';
    }

    private function getHariIndonesia($day)
    {
        $days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        return $days[$day] ?? $day;
    }
}
