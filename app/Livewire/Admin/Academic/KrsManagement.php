<?php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\StudyPlan;
use App\Models\AcademicPeriod;

class KrsManagement extends Component
{
    // State Pencarian Mahasiswa
    public $search_student = '';
    public $selectedStudent = null;

    // Data Akademik
    public $active_period;
    public $available_classes = [];
    public $taken_classes = [];

    // Filter Kelas
    public $search_class = '';

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
    }

    // 1. Cari & Pilih Mahasiswa
    public function selectStudent($studentId)
    {
        $this->selectedStudent = Student::with(['user', 'study_program'])->find($studentId);
        $this->search_student = ''; // Reset search bar
        $this->loadKrsData();
    }

    public function resetStudent()
    {
        $this->selectedStudent = null;
        $this->available_classes = [];
        $this->taken_classes = [];
    }

    // 2. Load Data KRS Mahasiswa Terpilih
    public function loadKrsData()
    {
        if (!$this->selectedStudent || !$this->active_period) return;

        // Ambil yang sudah diambil
        $this->taken_classes = StudyPlan::with(['classroom.course', 'classroom.schedules'])
            ->where('student_id', $this->selectedStudent->id)
            ->where('academic_period_id', $this->active_period->id)
            ->get();

        // Ambil ID kelas yang sudah diambil (untuk exclude)
        $takenIds = $this->taken_classes->pluck('classroom_id')->toArray();

        // Ambil kelas tersedia (Sesuai Prodi Mahasiswa)
        $this->available_classes = Classroom::with(['course', 'schedules', 'lecturer.user'])
            ->where('academic_period_id', $this->active_period->id)
            ->whereHas('course', function ($q) {
                // Filter Matkul Nama/Kode & Prodi Mahasiswa
                $q->where('study_program_id', $this->selectedStudent->study_program_id)
                    ->where(function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search_class . '%')
                            ->orWhere('code', 'like', '%' . $this->search_class . '%');
                    });
            })
            ->whereNotIn('id', $takenIds)
            ->take(20) // Limit biar gak berat
            ->get();
    }

    // 3. Admin Menambahkan Kelas (FORCE ADD)
    public function addClass($classId)
    {
        $class = Classroom::find($classId);

        // Validasi Sederhana (Admin bisa override, tapi kita kasih peringatan dasar)
        if ($class->enrolled >= $class->quota) {
            // Opsional: Admin bisa paksa masuk meski penuh? 
            // Untuk sekarang kita blokir dulu biar aman.
            session()->flash('error', 'Kelas Penuh!');
            return;
        }

        // Create Study Plan
        StudyPlan::create([
            'student_id' => $this->selectedStudent->id,
            'classroom_id' => $classId,
            'academic_period_id' => $this->active_period->id,
            'status' => 'APPROVED', // <--- BEDA DISINI: Admin input langsung Approve
        ]);

        $class->increment('enrolled'); // Update kuota

        session()->flash('success', 'Berhasil menambahkan mata kuliah.');
        $this->loadKrsData();
    }

    // 4. Admin Menghapus Kelas (FORCE DROP)
    public function removeClass($planId)
    {
        $plan = StudyPlan::find($planId);
        if ($plan) {
            $plan->classroom->decrement('enrolled');
            $plan->delete();
            session()->flash('success', 'Mata kuliah dihapus.');
            $this->loadKrsData();
        }
    }

    // Live Search Listener utk Kelas
    public function updatedSearchClass()
    {
        $this->loadKrsData();
    }

    public function render()
    {
        // Query Pencarian Mahasiswa (Dropdown)
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
