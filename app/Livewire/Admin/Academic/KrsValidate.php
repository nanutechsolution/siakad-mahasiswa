<?php

namespace App\Livewire\Admin\Academic;

use App\Enums\KrsStatus;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StudyPlan;
use App\Models\Student;
use App\Models\AcademicPeriod;

class KrsValidate extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_status; 
    public $active_period;

    // Untuk Modal Detail
    public $selectedPlan; // Sekarang kita simpan Model StudyPlan (Header)
    public $isModalOpen = false;
    public $rejection_notes = '';

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
        $this->filter_status = KrsStatus::SUBMITTED->value;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $plans = collect();

        if ($this->active_period) {
            $plans = StudyPlan::with(['student.user', 'student.studyProgram'])
                ->where('academic_period_id', $this->active_period->id)
                ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
                ->when($this->search, function ($q) {
                    $q->whereHas('student.user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('student', fn($s) => $s->where('nim', 'like', '%' . $this->search . '%'));
                })
                ->latest()
                ->paginate(10);
        }

        return view('livewire.admin.academic.krs-validate', [
            'plans' => $plans
        ])->layout('layouts.admin');
    }

    public function showDetail($planId)
    {
        $this->selectedPlan = StudyPlan::with([
            'student.user', 
            'details.courseClass.course', 
            'details.courseClass.classSchedules'
        ])->findOrFail($planId);

        $this->isModalOpen = true;
    }

    public function approve()
    {
        if ($this->selectedPlan) {
            $this->selectedPlan->update([
                'status' => KrsStatus::APPROVED,
                'approved_at' => now(),
                'notes' => null
            ]);

            session()->flash('message', 'KRS mahasiswa berhasil disetujui.');
            $this->closeModal();
        }
    }

    public function reject()
    {
        $this->validate([
            'rejection_notes' => 'required|min:5'
        ]);

        if ($this->selectedPlan) {
            $this->selectedPlan->update([
                'status' => KrsStatus::DRAFT, // Kembalikan ke draft agar mhs bisa edit
                'notes' => $this->rejection_notes
            ]);

            session()->flash('message', 'KRS ditolak dan dikembalikan ke mahasiswa.');
            $this->closeModal();
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['selectedPlan', 'rejection_notes']);
    }
}