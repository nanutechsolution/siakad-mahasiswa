<?php

namespace App\Livewire\Lecturer;

use App\Enums\KrsStatus;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan;

class KrsValidation extends Component
{
    use WithPagination;

    public $search = '';
    public $active_period;
    public $filter_status;
    
    // Modal State
    public $isModalOpen = false;
    public $selectedPlan = null; // Menggunakan Header StudyPlan
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
        $lecturer = Auth::user()->lecturer;
        $plans = collect();

        if ($lecturer && $this->active_period) {
            // Ambil StudyPlan (Header) milik mahasiswa bimbingan dosen ini
            $plans = StudyPlan::with(['student.user', 'student.studyProgram'])
                ->where('academic_period_id', $this->active_period->id)
                ->whereHas('student', function($q) use ($lecturer) {
                    $q->where('academic_advisor_id', $lecturer->id);
                })
                ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
                ->when($this->search, function($q) {
                    $q->whereHas('student.user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'))
                      ->orWhereHas('student', fn($s) => $s->where('nim', 'like', '%'.$this->search.'%'));
                })
                ->latest()
                ->paginate(10);
        }

        return view('livewire.lecturer.krs-validation', [
            'plans' => $plans
        ])->layout('layouts.lecturer');
    }

    public function showDetail($planId)
    {
        // Load Header dengan Detail, Kelas, Matkul, dan Jadwal
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

            session()->flash('message', 'KRS mahasiswa berhasil disetujui (ACC).');
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
                'status' => KrsStatus::DRAFT, // Kembalikan ke Draft agar bisa diedit mahasiswa
                'notes' => $this->rejection_notes
            ]);

            session()->flash('message', 'KRS ditolak dan dikembalikan ke mahasiswa untuk revisi.');
            $this->closeModal();
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['selectedPlan', 'rejection_notes']);
    }
}