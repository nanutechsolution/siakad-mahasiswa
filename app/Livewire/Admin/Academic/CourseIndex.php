<?php

namespace App\Livewire\Admin\Academic;

use App\Models\Course;
use App\Models\StudyProgram;
use Livewire\Component;
use Livewire\WithPagination;

class CourseIndex extends Component
{
    use WithPagination;

    // Filter & Search
    public $search = '';
    public $filter_prodi = '';
    public $paginate = 10;

    // Form Properties
    public $course_id, $study_program_id, $code, $name, $name_en;
    public $semester_default, $credit_total, $credit_theory = 0, $credit_practice = 0;
    public $is_active = true;
    public $group_code = 'MKK'; // Default
    public $is_mandatory = true;

    // Prerequisite Properties
    public $prerequisite_ids = []; 
    public $prerequisite_grades = []; // Properti untuk menyimpan nilai minimal (ID => Grade)

    // Modal State
    public $isModalOpen = false;
    public $isEditMode = false;

    /**
     * Lifecycle Hook: Berjalan otomatis saat matakuliah prasyarat dipilih/dihapus di UI.
     * Perbaikan: Menggunakan properti class langsung untuk menghindari error type mismatch dari Livewire.
     */
    public function updatedPrerequisiteIds()
    {
        // Pastikan kita bekerja dengan array
        $selectedIds = is_array($this->prerequisite_ids) ? $this->prerequisite_ids : [];
        
        $newGrades = [];
        foreach ($selectedIds as $id) {
            $idStr = (string)$id;
            // Jika sudah ada nilainya (perubahan grade), pertahankan. Jika baru dipilih, beri default 'C'
            $newGrades[$idStr] = $this->prerequisite_grades[$idStr] ?? 'C';
        }
        
        // Update array grades agar sinkron dengan ID yang masih terpilih saja
        $this->prerequisite_grades = $newGrades;
    }

    public function render()
    {
        // Eager load prerequisites untuk ditampilkan di tabel
        $courses = Course::with(['study_program', 'prerequisites'])
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter_prodi, function ($q) {
                $q->where('study_program_id', $this->filter_prodi);
            })
            ->orderBy('code', 'asc')
            ->paginate($this->paginate);

        // Ambil semua matakuliah untuk pilihan prasyarat di form
        // Kecuali matakuliah yang sedang diedit (untuk menghindari circular dependency)
        $all_courses = Course::where('is_active', true)
            ->when($this->course_id, fn($q) => $q->where('id', '!=', $this->course_id))
            ->orderBy('name')
            ->get();

        return view('livewire.admin.academic.course-index', [
            'courses' => $courses,
            'all_courses' => $all_courses,
            'prodis' => StudyProgram::all()
        ])->layout('layouts.admin');
    }

    // Buka Modal Tambah
    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    // Buka Modal Edit
    public function edit($id)
    {
        $course = Course::with('prerequisites')->find($id);
        $this->course_id = $id;
        $this->study_program_id = $course->study_program_id;
        $this->code = $course->code;
        $this->name = $course->name;
        $this->semester_default = $course->semester_default;
        $this->credit_total = $course->credit_total;
        $this->credit_theory = $course->credit_theory;
        $this->credit_practice = $course->credit_practice;
        $this->is_active = (bool) $course->is_active;
        $this->group_code = $course->group_code;
        $this->is_mandatory = (bool) $course->is_mandatory;

        // Load data prasyarat (ID dan Nilai Minimal dari pivot)
        $this->prerequisite_ids = [];
        $this->prerequisite_grades = [];

        foreach ($course->prerequisites as $pre) {
            $idStr = (string)$pre->id;
            $this->prerequisite_ids[] = $idStr;
            $this->prerequisite_grades[$idStr] = $pre->pivot->min_grade ?? 'C';
        }

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    // Simpan Data (Create/Update)
    public function store()
    {
        // Validasi
        $this->validate([
            'study_program_id' => 'required',
            'code' => 'required|unique:courses,code,' . $this->course_id,
            'name' => 'required',
            'semester_default' => 'required|numeric|min:1|max:8',
            'credit_total' => 'required|numeric|min:1',
            'group_code' => 'required',
            'is_mandatory' => 'boolean',
            'prerequisite_ids' => 'array',
            'prerequisite_grades' => 'array'
        ]);

        $course = Course::updateOrCreate(['id' => $this->course_id], [
            'study_program_id' => $this->study_program_id,
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'semester_default' => $this->semester_default,
            'credit_total' => $this->credit_total,
            'credit_theory' => $this->credit_theory,
            'credit_practice' => $this->credit_practice,
            'is_active' => $this->is_active,
            'group_code' => $this->group_code,
            'is_mandatory' => $this->is_mandatory,
        ]);

        // Siapkan data untuk sinkronisasi pivot (ID => ['min_grade' => VALUE])
        $syncData = [];
        $selectedIds = is_array($this->prerequisite_ids) ? $this->prerequisite_ids : [];
        
        foreach ($selectedIds as $id) {
            $idStr = (string)$id;
            $syncData[$id] = [
                'min_grade' => $this->prerequisite_grades[$idStr] ?? 'C'
            ];
        }

        // Sinkronisasi data prasyarat dengan data pivot
        $course->prerequisites()->sync($syncData);

        session()->flash('message', $this->isEditMode ? 'Matkul diperbarui!' : 'Matkul ditambahkan!');
        $this->closeModal();
    }

    public function delete($id)
    {
        Course::find($id)->delete();
        session()->flash('message', 'Matkul berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->course_id = null;
        $this->code = '';
        $this->name = '';
        $this->semester_default = '';
        $this->credit_total = '';
        $this->study_program_id = '';
        $this->is_active = true;
        $this->group_code = 'MKK';
        $this->is_mandatory = true;
        $this->prerequisite_ids = [];
        $this->prerequisite_grades = []; // Reset juga properti nilai
    }
}