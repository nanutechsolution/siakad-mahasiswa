<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\AcademicPeriod;
use App\Models\ActivityLog; // Pastikan model ini ada (opsional)
use App\Models\Billing;

class Dashboard extends Component
{
    public function render()
    {
        // 1. STATISTIK UTAMA
        $total_mhs = Student::where('status', 'A')->count();
        $total_dosen = Lecturer::where('is_active', true)->count();
        $total_prodi = StudyProgram::count();
        $semester_aktif = AcademicPeriod::where('is_active', true)->first();

        // 2. CHART: MAHASISWA PER PRODI
        // Kita hitung jumlah mhs aktif di setiap prodi
        $mhs_per_prodi = StudyProgram::withCount(['students' => function($q) {
            $q->where('status', 'A');
        }])->get();

        $chart_prodi_labels = $mhs_per_prodi->pluck('name')->toArray();
        $chart_prodi_values = $mhs_per_prodi->pluck('students_count')->toArray();

        // 3. CHART: KEUANGAN (LUNAS vs BELUM)
        // Hitung persentase pembayaran di semester aktif
        $paid_count = 0;
        $unpaid_count = 0;

        if ($semester_aktif) {
            $paid_count = Billing::where('academic_period_id', $semester_aktif->id)
                ->where('status', 'PAID')->count();
            $unpaid_count = Billing::where('academic_period_id', $semester_aktif->id)
                ->where('status', '!=', 'PAID')->count();
        }

        // 4. AKTIVITAS TERBARU (Opsional, jika tabel activity_logs ada)
        $activities = [];
        try {
            $activities = ActivityLog::with('user')->latest()->take(5)->get();
        } catch (\Exception $e) {
            // Abaikan jika tabel belum ada
        }

        return view('livewire.admin.dashboard', [
            'total_mhs' => $total_mhs,
            'total_dosen' => $total_dosen,
            'total_prodi' => $total_prodi,
            'semester_aktif' => $semester_aktif,
            
            // Data Chart
            'chart_prodi_labels' => $chart_prodi_labels,
            'chart_prodi_values' => $chart_prodi_values,
            'finance_stats' => [$paid_count, $unpaid_count],
            
            'activities' => $activities
        ])->layout('layouts.admin');
    }
}