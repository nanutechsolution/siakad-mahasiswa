<?php

namespace App\Livewire\Admin\Academic;

use App\Enums\KrsStatus;
use Livewire\Component;
use App\Models\Student;
use App\Models\CourseClass;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\AcademicPeriod;
use Illuminate\Support\Str;
use App\Traits\WithToast; // Opsional: Pastikan trait ini ada untuk notifikasi

class KrsManagement extends Component
{
    // State Pencarian Mahasiswa
    public $search_student = '';
    public $selectedStudent = null;

    // Data Akademik
    public $active_period;
    public $available_classes = [];
    public $selected_details = [];
    public $current_plan = null;

    // Filter Kelas
    public $search_class = '';
    public $total_sks = 0;

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
    }

    // 1. Cari & Pilih Mahasiswa
    public function selectStudent($studentId)
    {
        $this->selectedStudent = Student::with(['user', 'studyProgram'])->find($studentId);
        $this->search_student = ''; 
        $this->loadKrsData();
    }

    public function resetStudent()
    {
        $this->reset(['selectedStudent', 'available_classes', 'selected_details', 'current_plan', 'total_sks']);
    }

    // 2. Load Data KRS (Header & Detail)
    public function loadKrsData()
    {
        if (!$this->selectedStudent || !$this->active_period) return;

        // Ambil Header KRS
        $this->current_plan = StudyPlan::with(['details.courseClass.course', 'details.courseClass.classSchedules'])
            ->where('student_id', $this->selectedStudent->id)
            ->where('academic_period_id', $this->active_period->id)
            ->first();

        $takenClassIds = [];
        if ($this->current_plan) {
            $this->selected_details = $this->current_plan->details;
            $this->total_sks = $this->selected_details->sum(fn($d) => $d->courseClass->course->credit_total ?? 0);
            $takenClassIds = $this->selected_details->pluck('course_class_id')->toArray();
        } else {
            $this->selected_details = collect();
            $this->total_sks = 0;
        }

        // Ambil Kelas Tersedia (Sesuai Prodi Mahasiswa)
        $this->available_classes = CourseClass::with(['course', 'classSchedules'])
            ->where('academic_period_id', $this->active_period->id)
            ->where('is_active', true)
            ->whereHas('course', function ($q) {
                $q->where('study_program_id', $this->selectedStudent->study_program_id)
                  ->when($this->search_class, function ($sub) {
                      $sub->where('name', 'like', '%' . $this->search_class . '%')
                          ->orWhere('code', 'like', '%' . $this->search_class . '%');
                  });
            })
            ->whereNotIn('id', $takenClassIds)
            ->take(15)
            ->get();
    }

    // 3. Admin Menambahkan Kelas (Force Add)
    public function addClass($classId)
    {
        if (!$this->selectedStudent) return;

        // Buat Header jika belum ada
        if (!$this->current_plan) {
            $this->current_plan = StudyPlan::create([
                'id' => (string) Str::ulid(),
                'student_id' => $this->selectedStudent->id,
                'academic_period_id' => $this->active_period->id,
                'status' => KrsStatus::APPROVED->value, // Admin input langsung Approve
            ]);
        }

        // Cek apakah sudah ada di detail (mencegah double entry)
        $exists = StudyPlanDetail::where('study_plan_id', $this->current_plan->id)
            ->where('course_class_id', $classId)
            ->exists();

        if (!$exists) {
            StudyPlanDetail::create([
                'id' => (string) Str::ulid(),
                'study_plan_id' => $this->current_plan->id,
                'course_class_id' => $classId,
            ]);

            CourseClass::find($classId)->increment('enrolled_count');
            session()->flash('message', 'Mata kuliah berhasil ditambahkan secara paksa.');
        }

        $this->loadKrsData();
    }

    // 4. Admin Menghapus Kelas (Force Drop)
    public function removeClass($detailId)
    {
        $detail = StudyPlanDetail::with('courseClass')->find($detailId);
        if ($detail) {
            $detail->courseClass->decrement('enrolled_count');
            $detail->delete();
            session()->flash('message', 'Mata kuliah berhasil dihapus.');
            $this->loadKrsData();
        }
    }

    public function updatedSearchClass()
    {
        $this->loadKrsData();
    }

    public function render()
    {
        $students_result = [];
        if (strlen($this->search_student) > 2) {
            $students_result = Student::with('user')
                ->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $this->search_student . '%'))
                ->orWhere('nim', 'like', '%' . $this->search_student . '%')
                ->take(5)
                ->get();
        }

        return view('livewire.admin.academic.krs-management', [
            'students_result' => $students_result
        ])->layout('layouts.admin');
    }
}