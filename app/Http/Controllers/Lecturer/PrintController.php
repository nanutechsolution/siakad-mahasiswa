<?php

namespace App\Http\Controllers\Lecturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Classroom;
use App\Models\ClassMeeting;
use App\Models\Attendance;
use App\Models\Setting;

class PrintController extends Controller
{
    public function printAttendanceRecap($classId)
    {
        $lecturer = Auth::user()->lecturer;
        
        // 1. Validasi Kepemilikan Kelas
        $classroom = Classroom::with(['course', 'academic_period', 'study_plans.student.user'])
            ->where('id', $classId)
            ->where('lecturer_id', $lecturer->id)
            ->firstOrFail();

        // 2. Ambil Data Pertemuan
        $meetings = ClassMeeting::where('classroom_id', $classId)
            ->orderBy('meeting_no')
            ->get();

        // 3. Olah Data Presensi (Grid)
        $recap = [];
        foreach ($classroom->study_plans->where('status', 'APPROVED') as $plan) {
            $student = $plan->student;
            
            // Ambil semua absen mahasiswa ini di kelas ini
            $attendances = Attendance::whereHas('class_meeting', fn($q) => $q->where('classroom_id', $classId))
                ->where('student_id', $student->id)
                ->get()
                ->pluck('status', 'class_meeting_id'); // [meeting_id => 'H']

            $row = [
                'nim' => $student->nim,
                'name' => $student->user->name,
                'attendance_data' => []
            ];

            $hadir_count = 0;
            foreach ($meetings as $m) {
                $status = $attendances[$m->id] ?? '-';
                $row['attendance_data'][$m->meeting_no] = $status;
                if ($status == 'H') $hadir_count++;
            }
            
            $row['percent'] = $meetings->count() > 0 ? round(($hadir_count / $meetings->count()) * 100) : 0;
            
            $recap[] = $row;
        }

        // Sort by nama
        usort($recap, fn($a, $b) => strcmp($a['name'], $b['name']));

        $setting = Setting::first();

        $pdf = Pdf::loadView('pdf.attendance-recap', [
            'classroom' => $classroom,
            'meetings' => $meetings,
            'recap' => $recap,
            'setting' => $setting,
            'lecturer' => $lecturer,
            'printed_at' => now()->format('d F Y')
        ]);

        $pdf->setPaper('A4', 'landscape'); // Landscape agar muat banyak pertemuan

        return $pdf->stream('Rekap_Presensi_' . $classroom->course->code . '.pdf');
    }
}