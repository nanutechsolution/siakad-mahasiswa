<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\AcademicPeriod;
use Carbon\Carbon;

class Dashboard extends Component
{
    public $student;
    public $show_onboarding = false;

    public function mount()
    {
        $user = Auth::user();
        // Pastikan relasi 'student' ada di model User
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
        $krs_aktif_details = collect(); // Berisi item matkul (StudyPlanDetail)
        $jadwal_hari_ini = collect();
        $greeting = $this->getGreeting();

        $tagihan_belum_bayar = 0;
        $ipk = 0;
        $total_sks_kumulatif = 0;
        $sks_history_labels = [];
        $sks_history_values = [];

        // Target SKS Default
        $target_sks = 144; 

        if ($student) {
            $student->load('studyProgram');
            $target_sks = $student->studyProgram->total_credits ?? 144;

            // 1. Hitung Tagihan (Billings)
            // Asumsi relasi: Student -> hasMany(Billing)
            if (method_exists($student, 'billings')) {
                $billings = $student->billings()
                    ->where('status', '!=', 'paid') // Enum: unpaid, paid, cancelled
                    ->with('payments')
                    ->get();

                foreach ($billings as $bill) {
                    // Hitung total yang sudah dibayar (verified only)
                    $paid = $bill->payments->where('status', 'VERIFIED')->sum('amount_paid');
                    $sisa = $bill->amount - $paid;
                    if ($sisa > 0) {
                        $tagihan_belum_bayar += $sisa;
                    }
                }
            }

            // 2. Hitung Statistik Akademik (IPK & SKS)
            // Ambil semua detail matkul dari KRS yang sudah DISETUJUI
            $all_approved_details = StudyPlanDetail::whereHas('studyPlan', function($q) use ($student) {
                    $q->where('student_id', $student->id)
                      ->where('status', 'approved'); // Enum: draft, submitted, approved, rejected
                })
                ->whereNotNull('grade_point') // Hanya yang sudah ada nilai
                ->with(['courseClass.course', 'studyPlan.academicPeriod'])
                ->get();

            // Group by Course ID untuk menangani matkul ulang (Ambil nilai terbaik)
            $final_grades = $all_approved_details->groupBy(function($detail) {
                return $detail->courseClass->course_id ?? 'unknown';
            })->map(function ($attempts) {
                return $attempts->sortByDesc('grade_point')->first();
            });

            // Hitung Total Bobot & SKS
            $total_points = $final_grades->sum(fn($d) => ($d->courseClass->course->credit_total ?? 0) * ($d->grade_point ?? 0));
            $total_sks_kumulatif = $final_grades->sum(fn($d) => $d->courseClass->course->credit_total ?? 0);

            $ipk = $total_sks_kumulatif > 0 ? $total_points / $total_sks_kumulatif : 0;

            // 3. Data Grafik SKS per Semester
            // Group by Kode Periode (20241, 20242)
            $history = $all_approved_details->groupBy(function($detail) {
                return $detail->studyPlan->academicPeriod->code ?? 'N/A';
            })->map(function($group) {
                return $group->sum(fn($d) => $d->courseClass->course->credit_total ?? 0);
            })->sortKeys();

            $sks_history_labels = $history->keys()->values()->toArray();
            $sks_history_values = $history->values()->toArray();
        }

        // 4. Data Semester Ini (KRS Aktif & Jadwal)
        if ($student && $active_period) {
            // Ambil KRS Header semester ini
            $current_krs = StudyPlan::with([
                    'details.courseClass.course',
                    'details.courseClass.classSchedules' // Relasi ke tabel class_schedules
                ])
                ->where('student_id', $student->id)
                ->where('academic_period_id', $active_period->id)
                ->where('status', 'approved') // Hanya tampilkan jadwal jika KRS sudah disetujui
                ->first();

            if ($current_krs) {
                $krs_aktif_details = $current_krs->details; // Collection of StudyPlanDetail
                
                // Hitung SKS Semester Ini
                $total_sks_semester = $krs_aktif_details->sum(fn($d) => $d->courseClass->course->credit_total ?? 0);

                // Filter Jadwal Hari Ini
                $todayIso = Carbon::now()->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)

                $jadwal_hari_ini = $krs_aktif_details->flatMap(function ($detail) {
                    // Ambil jadwal dari kelas matkul ini
                    return $detail->courseClass->classSchedules->map(function ($sch) use ($detail) {
                        // Format objek untuk view
                        $obj = new \stdClass();
                        $obj->id = $sch->id;
                        $obj->course_name = $detail->courseClass->course->name ?? '-';
                        $obj->course_code = $detail->courseClass->course->code ?? '-';
                        $obj->class_name = $detail->courseClass->name ?? '-';
                        $obj->start_time = $sch->start_time; // Format H:i:s
                        $obj->end_time = $sch->end_time;
                        $obj->room = $sch->room_name ?? 'Online';
                        $obj->day_of_week = $sch->day_of_week;
                        return $obj;
                    });
                })->filter(function ($sch) use ($todayIso) {
                    // Bandingkan Integer (Lebih aman daripada string nama hari)
                    return $sch->day_of_week == $todayIso;
                })->sortBy('start_time');
            }
        }

        // Pengumuman (Opsional, pastikan model Announcement ada)
        $announcements = collect();
        if (class_exists(\App\Models\Announcement::class)) {
            $announcements = \App\Models\Announcement::where('is_active', true)
                ->whereIn('target_role', ['all', 'student'])
                ->latest()
                ->take(3)
                ->get();
        }

        return view('livewire.student.dashboard', [
            'student' => $student,
            'active_period' => $active_period,
            'krs_aktif' => $krs_aktif_details, // Kirim details, bukan header
            'total_sks_semester' => $total_sks_semester,
            'total_sks_kumulatif' => $total_sks_kumulatif,
            'target_sks' => $target_sks,
            'ipk' => $ipk,
            'sks_history_labels' => $sks_history_labels,
            'sks_history_values' => $sks_history_values,
            'jadwal_hari_ini' => $jadwal_hari_ini,
            'greeting' => $greeting,
            'tagihan_belum_bayar' => $tagihan_belum_bayar,
            'announcements' => $announcements
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
}