<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\AcademicPeriod;
use App\Models\ActivityLog;
use App\Models\Billing;

class Dashboard extends Component
{
    // Filter Properties
    public $selected_period_id;
    public $selected_prodi_id;

    public function mount()
    {
        // Default ke semester aktif & semua prodi
        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        $this->selected_period_id = $activePeriod ? $activePeriod->id : null;
        $this->selected_prodi_id = ''; // '' artinya semua prodi
    }

    // Trigger update chart saat filter berubah
    public function updated($property)
    {
        $this->dispatch('update-charts', [
            'prodi_values' => $this->getChartProdiData()['values'],
            'prodi_labels' => $this->getChartProdiData()['labels'],
            'finance_stats' => $this->getFinanceData()
        ]);
    }

    private function getChartProdiData()
    {
        // Chart ini tetap menampilkan semua prodi untuk perbandingan, 
        // tapi jika filter prodi aktif, kita bisa highlight (opsional), 
        // disini kita tetap load semua agar chart batang tetap informatif.
        $data = StudyProgram::withCount(['students' => function ($q) {
            $q->where('status', 'A');
        }])->get();

        return [
            'labels' => $data->pluck('name')->toArray(),
            'values' => $data->pluck('students_count')->toArray(),
        ];
    }

    private function getFinanceData()
    {
        // Hitung keuangan berdasarkan Periode yang dipilih
        if (!$this->selected_period_id) return [0, 0];

        $paid = Billing::where('academic_period_id', $this->selected_period_id)
            ->where('status', 'PAID');

        $unpaid = Billing::where('academic_period_id', $this->selected_period_id)
            ->where('status', '!=', 'PAID');

        // Jika filter prodi aktif, filter tagihan berdasarkan prodi mahasiswa
        if ($this->selected_prodi_id) {
            $paid->whereHas('student', fn($q) => $q->where('study_program_id', $this->selected_prodi_id));
            $unpaid->whereHas('student', fn($q) => $q->where('study_program_id', $this->selected_prodi_id));
        }

        return [$paid->count(), $unpaid->count()];
    }

    public function render()
    {
        // 1. STATISTIK UTAMA (Dinamis berdasarkan Filter)
        $total_mhs = Student::where('status', 'A')
            ->when($this->selected_prodi_id, fn($q) => $q->where('study_program_id', $this->selected_prodi_id))
            ->count();

        $total_dosen = Lecturer::where('is_active', true)
            ->when($this->selected_prodi_id, fn($q) => $q->where('study_program_id', $this->selected_prodi_id))
            ->count();

        // Total prodi tidak perlu difilter (tetap count global)
        $total_prodi = StudyProgram::count();

        // 2. DATA PENDUKUNG DROPDOWN
        $periods = AcademicPeriod::orderBy('name', 'desc')->take(10)->get(); // Ambil 10 semester terakhir
        $prodis = StudyProgram::orderBy('name')->get();

        // 3. CHART DATA (Initial Load)
        $chartData = $this->getChartProdiData();
        $financeStats = $this->getFinanceData();

        // 4. LOGS
        $activities = [];
        try {
            $activities = ActivityLog::with('user')->latest()->take(5)->get();
        } catch (\Exception $e) {
        }

        return view('livewire.admin.dashboard', [
            'total_mhs' => $total_mhs,
            'total_dosen' => $total_dosen,
            'total_prodi' => $total_prodi,
            'periods' => $periods,
            'prodis' => $prodis,
            'chart_prodi_labels' => $chartData['labels'],
            'chart_prodi_values' => $chartData['values'],   
            'finance_stats' => $financeStats,
            'activities' => $activities
        ])->layout('layouts.admin');
    }
}
