<?php

namespace App\Livewire\Admin\Academic;

use App\Models\Course;
use App\Models\StudyProgram;
use Livewire\Component;
use Livewire\WithPagination;

class CourseIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filter & Search
    public $search = '';
    public $filter_prodi = '';
    public $paginate = 10;

    // Form Properties
    public $course_id;
    public $study_program_id;
    public $code;
    public $name;
    public $name_en;
    public $semester_default;
    public $credit_total;
    public $credit_theory = 0;
    public $credit_practice = 0;
    public $group_code = 'MKK';
    public $is_mandatory = true;
    public $is_active = true;

    // Modal
    public $isModalOpen = false;
    public $isEditMode = false;

    public function render()
    {
        $courses = Course::with('study_program')
            ->when($this->search, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('name', 'like', '%' . $this->search . '%')
                       ->orWhere('code', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filter_prodi, fn ($q) =>
                $q->where('study_program_id', $this->filter_prodi)
            )
            ->orderBy('code')
            ->paginate($this->paginate);

        return view('livewire.admin.academic.course-index', [
            'courses' => $courses,
            'prodis'  => StudyProgram::orderBy('name')->get(),
        ])->layout('layouts.admin');
    }

    // =========================
    // CRUD
    // =========================

    public function create()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);

        $this->course_id         = $course->id;
        $this->study_program_id  = $course->study_program_id;
        $this->code              = $course->code;
        $this->name              = $course->name;
        $this->name_en           = $course->name_en;
        $this->semester_default  = $course->semester_default;
        $this->credit_total      = $course->credit_total;
        $this->credit_theory     = $course->credit_theory;
        $this->credit_practice   = $course->credit_practice;
        $this->group_code        = $course->group_code;
        $this->is_mandatory      = (bool) $course->is_mandatory;
        $this->is_active         = (bool) $course->is_active;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'study_program_id' => 'required|exists:study_programs,id',
            'code'             => 'required|string|max:20|unique:courses,code,' . $this->course_id,
            'name'             => 'required|string|max:255',
            'semester_default' => 'required|integer|min:1|max:14',
            'credit_total'     => 'required|integer|min:1|max:30',
            'credit_theory'    => 'required|integer|min:0',
            'credit_practice'  => 'required|integer|min:0',
            'group_code'       => 'required|string|max:10',
            'is_mandatory'     => 'boolean',
            'is_active'        => 'boolean',
        ]);

        Course::updateOrCreate(
            ['id' => $this->course_id],
            [
                'study_program_id' => $this->study_program_id,
                'code'             => strtoupper($this->code),
                'name'             => $this->name,
                'name_en'          => $this->name_en,
                'semester_default' => $this->semester_default,
                'credit_total'     => $this->credit_total,
                'credit_theory'    => $this->credit_theory,
                'credit_practice'  => $this->credit_practice,
                'group_code'       => $this->group_code,
                'is_mandatory'     => $this->is_mandatory,
                'is_active'        => $this->is_active,
            ]
        );

        session()->flash(
            'message',
            $this->isEditMode
                ? '✅ Mata kuliah berhasil diperbarui'
                : '✅ Mata kuliah berhasil ditambahkan'
        );

        $this->closeModal();
    }

    public function delete($id)
    {
        Course::findOrFail($id)->delete();

        session()->flash('message', '🗑️ Mata kuliah berhasil dihapus');
    }

    // =========================
    // Helpers
    // =========================

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->course_id = null;
        $this->study_program_id = '';
        $this->code = '';
        $this->name = '';
        $this->name_en = '';
        $this->semester_default = '';
        $this->credit_total = '';
        $this->credit_theory = 0;
        $this->credit_practice = 0;
        $this->group_code = 'MKK';
        $this->is_mandatory = true;
        $this->is_active = true;
    }
}
