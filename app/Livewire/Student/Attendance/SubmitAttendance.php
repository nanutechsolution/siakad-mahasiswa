<?php

namespace App\Livewire\Student\Attendance;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\ClassMeeting;
use App\Models\Attendance;
use App\Models\StudyPlan;
use Carbon\Carbon;

class SubmitAttendance extends Component
{
    public $token;
    public $student;
    
    // History presensi hari ini
    public $today_logs;

    public function mount()
    {
        $this->student = Auth::user()->student;
        $this->refreshLogs();
    }

    public function refreshLogs()
    {
        // Ambil riwayat absen mahasiswa ini yang sudah H/I/S
        $this->today_logs = Attendance::with(['class_meeting.classroom.course'])
            ->where('student_id', $this->student->id)
            ->where('status', '!=', 'A') // Bukan Alpha
            ->whereDate('updated_at', Carbon::today())
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function submit()
    {
        $this->validate([
            'token' => 'required|string|size:6',
        ]);

        $inputToken = strtoupper($this->token);

        // 1. Cari Pertemuan yang Tokennya Cocok & Sedang Buka
        $meeting = ClassMeeting::where('token', $inputToken)
            ->where('is_open', true)
            ->first();

        if (!$meeting) {
            session()->flash('error', 'Token salah atau sesi presensi sudah ditutup.');
            return;
        }

        // 2. Validasi: Apakah mahasiswa mengambil kelas ini? (Cek KRS)
        $isEnrolled = StudyPlan::where('student_id', $this->student->id)
            ->where('classroom_id', $meeting->classroom_id)
            ->where('status', 'APPROVED')
            ->exists();

        if (!$isEnrolled) {
            session()->flash('error', 'Anda tidak terdaftar di kelas ini.');
            return;
        }

        // 3. Proses Absen (Update Status)
        // Gunakan updateOrCreate untuk keamanan (meski harusnya record sudah dibuat oleh dosen)
        Attendance::updateOrCreate(
            [
                'class_meeting_id' => $meeting->id,
                'student_id' => $this->student->id
            ],
            [
                'status' => 'H', // Hadir
                'check_in_at' => now()
            ]
        );

        session()->flash('success', 'Berhasil! Kehadiran Anda tercatat.');
        $this->token = ''; // Reset input
        $this->refreshLogs(); // Refresh history bawah
    }

    public function render()
    {
        return view('livewire.student.attendance.submit-attendance')->layout('layouts.student');
    }
}