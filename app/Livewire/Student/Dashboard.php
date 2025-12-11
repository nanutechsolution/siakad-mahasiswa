<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\StudyPlan;
use App\Models\AcademicPeriod;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $student;
    public $show_onboarding = false;

    public function mount()
    {

        $user = Auth::user();
        $this->student = $user->student;

        // CEK APAKAH MAHASISWA BARU?
        if ($this->student && $this->student->is_new_student) {
            $this->show_onboarding = true;
        }
    }
    public function dismissOnboarding()
    {
        if ($this->student) {
            $this->student->update(['is_new_student' => false]);
            $this->show_onboarding = false;
        }
    }
    public function render()
    {
        $student = $this->student;
        $active_period = AcademicPeriod::where('is_active', true)->first();


        // Init Variabel
        $total_sks_semester = 0;
        $krs_aktif = collect();
        $jadwal_hari_ini = collect();
        $greeting = $this->getGreeting();

        $tagihan_belum_bayar = 0;
        $ipk = 0;
        $total_sks_kumulatif = 0;
        $sks_history_labels = [];
        $sks_history_values = [];

        // --- TAMBAHAN: TARGET SKS ---
        $target_sks = 144; // Default S1

        if ($student) {

            // Tentukan target berdasarkan jenjang prodi
            // Pastikan load relasi study_program
            $student->load('study_program');

            $degree = $student->study_program->degree ?? 'S1';
            $target_sks = $student->study_program->total_credits ?? 144;



            // 1. Hitung Tagihan (Keuangan)
            $billings = $student->billings()
                ->where('status', '!=', 'PAID')
                ->with('payments')
                ->get();

            foreach ($billings as $bill) {
                $paid = $bill->payments->where('status', 'VERIFIED')->sum('amount_paid');
                $sisa = $bill->amount - $paid;
                $tagihan_belum_bayar += $sisa;
            }

            // 2. Hitung Statistik Akademik (IPK & SKS Kumulatif)
            $all_approved_krs = StudyPlan::with(['classroom.course', 'academic_period'])
                ->where('student_id', $student->id)
                ->where('status', 'APPROVED')
                ->get();

            // 2. Hitung Statistik Akademik (IPK & SKS Kumulatif)
            // Hanya ambil matkul yang SUDAH LULUS/APPROVED (Nilai sudah keluar)
            $raw_grades = StudyPlan::with(['classroom.course', 'academic_period'])
                ->where('student_id', $student->id)
                ->where('status', 'APPROVED')
                ->whereNotNull('grade_point') // Hanya yang sudah ada nilai
                ->get();
            $final_grades = $raw_grades->groupBy('classroom.course_id')->map(function ($attempts) {
                // Ambil percobaan dengan Bobot (grade_point) tertinggi
                return $attempts->sortByDesc('grade_point')->first();
            });


            // $total_points = $all_approved_krs->sum(fn($k) => $k->classroom->course->credit_total * $k->grade_point);
            // $total_sks_kumulatif = $all_approved_krs->sum(fn($k) => $k->classroom->course->credit_total);
            $total_points = $final_grades->sum(fn($k) => $k->classroom->course->credit_total * $k->grade_point);
            $total_sks_kumulatif = $final_grades->sum(fn($k) => $k->classroom->course->credit_total);


            $ipk = $total_sks_kumulatif > 0 ? $total_points / $total_sks_kumulatif : 0;

            // 3. Data Grafik SKS per Semester
            $history = $all_approved_krs->groupBy('academic_period.code')
                ->map(fn($group) => $group->sum(fn($k) => $k->classroom->course->credit_total))
                ->sortKeys();

            $sks_history_labels = $history->keys()->values()->toArray();
            $sks_history_values = $history->values()->toArray();
        }

        if ($student && $active_period) {
            // 4. Data Semester Ini (KRS Aktif)
            $krs_aktif = StudyPlan::with(['classroom.course', 'classroom.schedules', 'classroom.lecturer.user'])
                ->where('student_id', $student->id)
                ->where('academic_period_id', $active_period->id)
                ->get();
            $total_sks_semester = $krs_aktif->sum(fn($k) => $k->classroom->course->credit_total);
            $hari_inggris = Carbon::now()->timezone('Asia/Makassar')->format('l');
            $hari_indo = $this->mapHariToIndo($hari_inggris);
            $jadwal_hari_ini = $krs_aktif->flatMap(function ($krs) {
                return $krs->classroom->schedules->map(function ($sch) use ($krs) {
                    $sch->course_name = $krs->classroom->course->name;
                    $sch->class_name = $krs->classroom->name;
                    $sch->lecturer_name = $krs->classroom->lecturer->user->name ?? '-';
                    return $sch;
                });
            })->filter(function ($sch) use ($hari_indo) {
                // Bandingkan Case-Insensitive (biar senin == Senin)
                return strtolower($sch->day) == strtolower($hari_indo);
            })->sortBy('start_time');
        }

        $announcements = \App\Models\Announcement::where('is_active', true)
        ->whereIn('target_role', ['all', 'student'])
        ->latest()
        ->take(3)
        ->get();

        return view('livewire.student.dashboard', [
            'student' => $student,
            'active_period' => $active_period,
            'krs_aktif' => $krs_aktif,
            'total_sks_semester' => $total_sks_semester,
            'total_sks_kumulatif' => $total_sks_kumulatif,
            'target_sks' => $target_sks, // <-- Kirim variable ini ke View
            'ipk' => $ipk,
            'sks_history_labels' => $sks_history_labels,
            'sks_history_values' => $sks_history_values,
            'jadwal_hari_ini' => $jadwal_hari_ini,
            'greeting' => $greeting,
            'tagihan_belum_bayar' => $tagihan_belum_bayar,
            'announcements' => $announcements // Kirim ke view
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

    private function mapHariToIndo($dayInEnglish)
    {
        $map = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        return $map[$dayInEnglish] ?? $dayInEnglish;
    }

    private function getHariIndonesia($day)
    {
        $days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        return $days[$day] ?? $day;
    }
}
