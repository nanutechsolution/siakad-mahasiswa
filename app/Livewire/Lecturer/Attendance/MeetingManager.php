<?php

namespace App\Livewire\Lecturer\Attendance;

use Livewire\Component;
use App\Models\Classroom;
use App\Models\ClassMeeting;
use App\Models\Attendance;
use App\Models\StudyPlan;
use Illuminate\Support\Str;

class MeetingManager extends Component
{
    public $classroom;
    public $meetings;
    
    // State Buka Kelas
    public $active_meeting_id;
    public $token;
    public $topic;
    
    // State Detail & Rekap
    public $selected_meeting;
    public $attendance_list = [];
    public $show_recap = false; // Mode Rekap
    public $recap_data = [];    // Data Rekap

    public function mount($classId)
    {
        // Load kelas beserta mahasiswa yang mengambil (KRS Approved)
        $this->classroom = Classroom::with(['course', 'schedules', 'study_plans' => function($q) {
            $q->where('status', 'APPROVED')->with('student.user');
        }])->findOrFail($classId);

        $this->refreshMeetings();
    }

    public function refreshMeetings()
    {
        $this->meetings = ClassMeeting::where('classroom_id', $this->classroom->id)
            ->orderBy('meeting_no')
            ->get();
    }

    // ... (createMeeting, openAttendance, closeAttendance tetap sama) ...
    public function createMeeting()
    {
        $nextNo = $this->meetings->count() + 1;
        
        ClassMeeting::create([
            'classroom_id' => $this->classroom->id,
            'meeting_no' => $nextNo,
            'meeting_date' => now(),
            'topic' => 'Pertemuan Ke-' . $nextNo,
            'is_open' => false,
        ]);
        
        $this->refreshMeetings();
    }

    public function openAttendance($meetingId)
    {
        $meeting = ClassMeeting::find($meetingId);
        $token = strtoupper(Str::random(6)); 
        
        $meeting->update(['is_open' => true, 'token' => $token]);

        // Inisialisasi ALFA untuk semua mahasiswa
        $students = $this->classroom->study_plans; // Ambil dari relation yg sudah di-load

        foreach ($students as $plan) {
            Attendance::firstOrCreate([
                'class_meeting_id' => $meeting->id,
                'student_id' => $plan->student_id
            ], ['status' => 'A']);
        }

        session()->flash('message', "Sesi Absensi Dibuka! Token: $token");
        $this->refreshMeetings();
    }

    public function closeAttendance($meetingId)
    {
        ClassMeeting::where('id', $meetingId)->update(['is_open' => false]);
        $this->refreshMeetings();
    }

    // LIHAT DETAIL PERTEMUAN
    public function showDetail($meetingId)
    {
        $this->show_recap = false; // Matikan mode rekap
        $this->selected_meeting = ClassMeeting::find($meetingId);
        
        $this->attendance_list = Attendance::with('student.user')
            ->where('class_meeting_id', $meetingId)
            ->get()
            ->sortBy(fn($att) => $att->student->user->name);
    }

    // UPDATE STATUS MANUAL
    public function updateStatus($attendanceId, $status)
    {
        Attendance::where('id', $attendanceId)->update([
            'status' => $status,
            'check_in_at' => ($status == 'H') ? now() : null
        ]);
        
        $this->showDetail($this->selected_meeting->id);
    }

    // --- FITUR BARU: LIHAT REKAP ---
    public function showRecap()
    {
        $this->show_recap = true;
        $this->selected_meeting = null;

        // Hitung Rekap Per Mahasiswa
        // Kita loop semua mahasiswa yang ambil kelas ini
        $recap = [];
        $total_meetings = $this->meetings->count();

        foreach ($this->classroom->study_plans as $plan) {
            $student = $plan->student;
            
            // Ambil semua presensi mahasiswa ini di kelas ini
            $attendances = Attendance::whereHas('class_meeting', function($q) {
                $q->where('classroom_id', $this->classroom->id);
            })->where('student_id', $student->id)->get();

            $hadir = $attendances->where('status', 'H')->count();
            $izin = $attendances->where('status', 'I')->count();
            $sakit = $attendances->where('status', 'S')->count();
            $alpha = $attendances->where('status', 'A')->count();
            
            // Persentase Kehadiran (Hadir / Total Pertemuan yg sudah dibuat)
            $percentage = $total_meetings > 0 ? round(($hadir / $total_meetings) * 100) : 0;

            $recap[] = [
                'name' => $student->user->name,
                'nim' => $student->nim,
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'percentage' => $percentage
            ];
        }

        // Sort by nama
        usort($recap, fn($a, $b) => strcmp($a['name'], $b['name']));
        
        $this->recap_data = $recap;
    }

    public function render()
    {
        return view('livewire.lecturer.attendance.meeting-manager')->layout('layouts.lecturer');
    }
}