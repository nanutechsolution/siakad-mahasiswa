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
    public $filter_status; // Default cari yang pending
    public $active_period;

    // Untuk Modal Detail
    public $selectedStudent;
    public $studentPlans = [];
    public $isModalOpen = false;

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
        $this->filter_status = KrsStatus::SUBMITTED->value;
    }

    public function render()
    {
        $students = collect();

        if ($this->active_period) {
            // CARI MAHASISWA YANG PUNYA KRS DI SEMESTER INI
            $students = Student::with(['user', 'study_program'])
                ->whereHas('study_plans', function ($q) {
                    // Filter berdasarkan Status (Pending/Approved)
                    // dan Semester Aktif
                    $q->where('academic_period_id', $this->active_period->id)
                        ->where('status', $this->filter_status); // <--- KUNCI PENCARIAN
                })
                ->when($this->search, function ($q) {
                    $q->whereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'))
                        ->orWhere('nim', 'like', '%' . $this->search . '%');
                })
                ->paginate(10);
        }

        return view('livewire.admin.academic.krs-validate', [
            'students' => $students
        ])->layout('layouts.admin');
    }

    // TAMPILKAN DETAIL MATKUL MAHASISWA
    public function showDetail($studentId)
    {
        $this->selectedStudent = Student::with('user')->find($studentId);

        // Ambil semua rencana studi dia di semester ini
        $this->studentPlans = StudyPlan::with(['classroom.course', 'classroom.schedules'])
            ->where('student_id', $studentId)
            ->where('academic_period_id', $this->active_period->id)
            ->get();

        $this->isModalOpen = true;
    }

    // SETUJUI SEMUA MATKUL (ACC KRS)
    public function approve()
    {
        if ($this->selectedStudent) {
            // Update semua item jadi APPROVED
            StudyPlan::where('student_id', $this->selectedStudent->id)
                ->where('academic_period_id', $this->active_period->id)
                ->update(['status' => KrsStatus::APPROVED]);

            session()->flash('message', 'KRS Mahasiswa ' . $this->selectedStudent->user->name . ' berhasil Disetujui!');
            $this->isModalOpen = false;
        }
    }

    // TOLAK / KEMBALIKAN KE DRAFT
    public function reject()
    {
        if ($this->selectedStudent) {
            StudyPlan::where('student_id', $this->selectedStudent->id)
                ->where('academic_period_id', $this->active_period->id)
                ->update(['status' => KrsStatus::DRAFT]);

            session()->flash('message', 'KRS dikembalikan ke status Draft.');
            $this->isModalOpen = false;
        }
    }
}
