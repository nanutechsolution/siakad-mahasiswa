<?php

namespace App\Livewire\Admin;

use App\Enums\KrsStatus;
use App\Models\AcademicPeriod;
use App\Models\Billing;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyProgram;
use Livewire\Component;
use Illuminate\Support\Facades\Schema;

class Dashboard extends Component
{
    public $selected_period_id;
    public $selected_prodi_id;

    public function mount()
    {
        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        $this->selected_period_id = $activePeriod?->id;
        $this->selected_prodi_id = '';
    }

    public function updated($property)
    {
        $this->dispatch('update-charts', [
            'prodi_data' => $this->getChartProdiData(),
            'krs_data' => $this->getKrsStats(),
            'finance_stats' => $this->getFinanceData()
        ]);
    }

    private function getChartProdiData()
    {
        $data = StudyProgram::withCount(['students' => fn($q) => $q->where('status', 'active')])->get();
        return [
            'labels' => $data->pluck('name')->toArray(),
            'values' => $data->pluck('students_count')->toArray(),
        ];
    }

    private function getKrsStats()
    {
        if (!$this->selected_period_id) return ['labels' => [], 'values' => []];

        $baseQuery = StudyPlan::where('academic_period_id', $this->selected_period_id)
            ->when($this->selected_prodi_id, fn($q) => $q->whereHas('student', fn($s) => $s->where('study_program_id', $this->selected_prodi_id)));

        $stats = [
            KrsStatus::DRAFT->label() => (clone $baseQuery)->where('status', KrsStatus::DRAFT->value)->count(),
            KrsStatus::SUBMITTED->label() => (clone $baseQuery)->where('status', KrsStatus::SUBMITTED->value)->count(),
            KrsStatus::APPROVED->label() => (clone $baseQuery)->where('status', KrsStatus::APPROVED->value)->count(),
        ];

        return [
            'labels' => array_keys($stats),
            'values' => array_values($stats),
        ];
    }

    private function getFinanceData()
    {
        if (!$this->selected_period_id) return [0, 0];

        $query = Billing::where('academic_period_id', $this->selected_period_id)
            ->when($this->selected_prodi_id, fn($q) => $q->whereHas('student', fn($s) => $s->where('study_program_id', $this->selected_prodi_id)));

        $paid = (clone $query)->where('status', 'paid')->sum('amount');
        $unpaid = (clone $query)->where('status', 'unpaid')->sum('amount');

        return [(int)$paid, (int)$unpaid];
    }

    public function render()
    {
        // Cek apakah tabel lecturer menggunakan 'status' atau 'is_active' (Fallback logic)
        $lecturerQuery = Lecturer::query()
            ->when($this->selected_prodi_id, fn($q) => $q->where('study_program_id', $this->selected_prodi_id));
        
        if (Schema::hasColumn('lecturers', 'status')) {
            $lecturerQuery->where('status', 'active');
        } elseif (Schema::hasColumn('lecturers', 'is_active')) {
            $lecturerQuery->where('is_active', true);
        }

        return view('livewire.admin.dashboard', [
            'total_mhs' => Student::where('status', 'active')->when($this->selected_prodi_id, fn($q) => $q->where('study_program_id', $this->selected_prodi_id))->count(),
            'total_dosen' => $lecturerQuery->count(),
            'total_prodi' => StudyProgram::count(),
            'periods' => AcademicPeriod::orderBy('code', 'desc')->take(5)->get(),
            'prodis' => StudyProgram::orderBy('name')->get(),
            'krs_count' => StudyPlan::where('academic_period_id', $this->selected_period_id)->count(),
            'chart_prodi' => $this->getChartProdiData(),
            'chart_krs' => $this->getKrsStats(),
            'finance' => $this->getFinanceData()
        ])->layout('layouts.admin');
    }
}