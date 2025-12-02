<?php

namespace App\Livewire\Lecturer;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\AcademicPeriod;
use App\Models\StudyPlan;
use App\Enums\KrsStatus; // Pastikan Enum ini ada

class KrsValidation extends Component
{
    use WithPagination;

    public $search = '';
    public $active_period;
    
    // Modal State
    public $isModalOpen = false;
    public $selectedStudent = null;
    public $studentPlans = [];

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
    }

    public function render()
    {
        $lecturer = Auth::user()->lecturer;
        $students = collect();

        if ($lecturer && $this->active_period) {
            // Ambil Mahasiswa Bimbingan yang sudah SUBMITTED / APPROVED KRS-nya
            $students = Student::with(['user', 'study_program'])
                ->where('academic_advisor_id', $lecturer->id) // Filter Bimbingan
                ->whereHas('study_plans', function($q) {
                    $q->where('academic_period_id', $this->active_period->id)
                      ->whereIn('status', [KrsStatus::SUBMITTED, KrsStatus::APPROVED]);
                })
                ->when($this->search, function($q) {
                    $q->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'))
                      ->orWhere('nim', 'like', '%'.$this->search.'%');
                })
                ->paginate(10);
        }

        return view('livewire.lecturer.krs-validation', [
            'students' => $students
        ])->layout('layouts.lecturer');
    }

    public function showDetail($studentId)
    {
        $this->selectedStudent = Student::with('user')->find($studentId);
        
        // Ambil KRS mahasiswa ini
        $this->studentPlans = StudyPlan::with(['classroom.course', 'classroom.schedules'])
            ->where('student_id', $studentId)
            ->where('academic_period_id', $this->active_period->id)
            ->get();

        $this->isModalOpen = true;
    }

    public function approve()
    {
        if ($this->selectedStudent) {
            StudyPlan::where('student_id', $this->selectedStudent->id)
                ->where('academic_period_id', $this->active_period->id)
                ->where('status', KrsStatus::SUBMITTED) // Hanya yg submitted
                ->update(['status' => KrsStatus::APPROVED]);

            session()->flash('message', 'KRS Mahasiswa berhasil disetujui (ACC).');
            $this->isModalOpen = false;
        }
    }

    public function reject()
    {
        if ($this->selectedStudent) {
            StudyPlan::where('student_id', $this->selectedStudent->id)
                ->where('academic_period_id', $this->active_period->id)
                ->update(['status' => KrsStatus::DRAFT]); // Kembalikan ke Draft

            session()->flash('message', 'KRS dikembalikan ke mahasiswa untuk revisi.');
            $this->isModalOpen = false;
        }
    }
}