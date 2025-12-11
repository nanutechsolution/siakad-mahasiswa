<?php

namespace App\Livewire\Student\Attendance;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan;
use App\Models\Attendance;
use App\Models\ClassMeeting;

class AttendanceRecap extends Component
{
    public function render()
    {
        $student = Auth::user()->student;
        $active_period = AcademicPeriod::where('is_active', true)->first();
        
        $recap = [];

        if ($student && $active_period) {
            // 1. Ambil Mata Kuliah yang diambil (APPROVED)
            $plans = StudyPlan::with(['classroom.course'])
                ->where('student_id', $student->id)
                ->where('academic_period_id', $active_period->id)
                ->where('status', 'APPROVED')
                ->get();

            foreach ($plans as $plan) {
                $classId = $plan->classroom_id;
                
                // 2. Ambil Semua Pertemuan Kelas Tersebut
                $meetings = ClassMeeting::where('classroom_id', $classId)
                    ->orderBy('meeting_no')
                    ->get();
                
                // 3. Ambil Status Kehadiran Mahasiswa
                $attendances = Attendance::where('student_id', $student->id)
                    ->whereIn('class_meeting_id', $meetings->pluck('id'))
                    ->get()
                    ->keyBy('class_meeting_id');

                $history = [];
                $presentCount = 0;
                $totalMeetings = $meetings->count();

                foreach ($meetings as $meeting) {
                    // Cek status. Jika belum ada record (dosen belum buka absen), anggap '-'
                    $status = isset($attendances[$meeting->id]) ? $attendances[$meeting->id]->status : '-';
                    
                    if ($status == 'H') $presentCount++;

                    $history[] = [
                        'no' => $meeting->meeting_no,
                        'date' => $meeting->meeting_date->format('d M'),
                        'topic' => $meeting->topic,
                        'status' => $status
                    ];
                }

                // Hitung Persentase
                $percentage = $totalMeetings > 0 ? round(($presentCount / $totalMeetings) * 100) : 0;

                // Tentukan Warna Progress Bar
                $color = 'bg-red-500'; // Bahaya (< 50%)
                if($percentage >= 75) $color = 'bg-green-500'; // Aman
                elseif($percentage >= 50) $color = 'bg-yellow-500'; // Waspada

                $recap[] = [
                    'course_name' => $plan->classroom->course->name,
                    'course_code' => $plan->classroom->course->code,
                    'class_name' => $plan->classroom->name,
                    'total' => $totalMeetings,
                    'present' => $presentCount,
                    'percent' => $percentage,
                    'color' => $color,
                    'history' => $history
                ];
            }
        }

        return view('livewire.student.attendance.attendance-recap', [
            'recap' => $recap
        ])->layout('layouts.student');
    }
}