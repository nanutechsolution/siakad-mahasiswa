<?php

namespace App\Livewire\Admin\Lpm;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lecturer;
use App\Models\AcademicPeriod;
use App\Models\StudyProgram;

class EdomResult extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_prodi = '';
    public $active_period;

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
    }

    public function render()
    {
        $lecturers = collect();

        if ($this->active_period) {
            $lecturers = Lecturer::with(['user', 'study_program'])
                // Hitung Rata-rata Skor (Avg Score) khusus semester ini
                ->withAvg(['edom_responses as avg_score' => function($q) {
                    // PERBAIKAN: Tambahkan 'edom_responses.' sebelum nama kolom
                    $q->where('edom_responses.academic_period_id', $this->active_period->id);
                }], 'score')
                
                // Hitung Jumlah Responden
                ->withCount(['edom_responses as total_respondents' => function($q) {
                     // PERBAIKAN: Tambahkan 'edom_responses.' sebelum nama kolom
                     $q->where('edom_responses.academic_period_id', $this->active_period->id);
                }])
                
                // Filter Pencarian
                ->when($this->search, function($q) {
                    $q->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'));
                })
                ->when($this->filter_prodi, function($q) {
                    $q->where('study_program_id', $this->filter_prodi);
                })
                // Urutkan dari Nilai Tertinggi (Ranking)
                ->orderByDesc('avg_score')
                ->paginate(10);
        }

        return view('livewire.admin.lpm.edom-result', [
            'lecturers' => $lecturers,
            'prodis' => StudyProgram::all()
        ])->layout('layouts.admin');
    }
}