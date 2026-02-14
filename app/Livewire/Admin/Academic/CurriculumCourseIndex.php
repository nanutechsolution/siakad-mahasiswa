<?php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\Curriculum;
use App\Models\CurriculumCourse;
use App\Models\CourseGroup; // Pastikan model ini ada
use Illuminate\Support\Str; // WAJIB: Untuk Generate ULID
use Illuminate\Validation\Rule;

class CurriculumCourseIndex extends Component
{
    use WithPagination;

    // Filter & Search
    public $search = '';
    public $filter_prodi = '';
    public $filter_year = '';

    // Modal State
    public $isModalOpen = false;
    public $isEditMode = false;
    public $editId = null;

    // Form Fields
    public $curriculum_id;
    public $course_id;
    public $semester;
    public $is_mandatory = false;
    public $credit_total;
    public $credit_theory;
    public $credit_practice;
    public $course_group_id;

    // Validation Rules (Dynamic)
    protected function rules()
    {
        return [
            'curriculum_id' => 'required|exists:curriculums,id',
            'course_id' => [
                'required',
                'exists:courses,id',
                // Cek Unik: Kombinasi Curriculum + Course tidak boleh kembar
                Rule::unique('curriculum_courses', 'course_id')
                    ->where(function ($query) {
                        return $query->where('curriculum_id', $this->curriculum_id);
                    })
                    ->ignore($this->editId)
            ],
            'semester' => 'required|integer|min:1|max:8',
            'course_group_id' => 'required|exists:course_groups,id',
            'is_mandatory' => 'boolean',
            'credit_total' => 'required|integer|min:0',
            'credit_theory' => 'nullable|integer|min:0',
            'credit_practice' => 'nullable|integer|min:0',
        ];
    }

    public function mount()
    {
        // Set default group jika ada
        $defaultGroup = CourseGroup::where('code', 'MKK')->first();
        $this->course_group_id = $defaultGroup->id ?? null;
    }

    // Reset pagination saat filter berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedFilterProdi()
    {
        $this->resetPage();
    }
    public function updatedFilterYear()
    {
        $this->resetPage();
    }

    // Auto-fill SKS saat Course dipilih (Optional UX Improvement)
    public function updatedCourseId($value)
    {
        if ($course = Course::find($value)) {
            // Jika di tabel master course ada kolom sks, bisa di-load disini
            // $this->credit_total = $course->credit_default; 
        }
    }

    public function render()
    {
        $query = CurriculumCourse::with(['curriculum.studyProgram', 'course']);

        // Search Logic
        if ($this->search) {
            $query->whereHas('course', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('code', 'like', "%{$this->search}%");
            });
        }

        // Filter Prodi
        if ($this->filter_prodi) {
            $query->whereHas('curriculum', function ($q) {
                $q->where('study_program_id', $this->filter_prodi);
            });
        }

        // Filter Tahun
        if ($this->filter_year) {
            $query->whereHas('curriculum', function ($q) {
                $q->where('year', $this->filter_year);
            });
        }

        // Load Data Dropdown (Optimized)
        // Note: Jika data ribuan, sebaiknya pindahkan ini ke computed property atau load hanya saat modal buka
        $dropdowns = [
            'prodis' => \App\Models\StudyProgram::orderBy('name')->get(['id', 'name']),
            'curriculum_years' => Curriculum::select('year')->distinct()->orderBy('year', 'desc')->pluck('year'),
            'curriculums' => Curriculum::with('studyProgram')->orderBy('year', 'desc')->get(['id', 'name', 'year', 'study_program_id']),
            'courses' => Course::orderBy('code')->get(['id', 'code', 'name']),
            'courseGroups' => CourseGroup::orderBy('code')->get(['id', 'code', 'name']),
        ];

        return view('livewire.admin.academic.curriculum-course-index', array_merge(
            [
                'curriculum_courses' => $query->orderBy('semester')->orderBy('updated_at', 'desc')->paginate(10)
            ],
            $dropdowns
        ))->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->isEditMode = false;
    }

    public function edit($id)
    {
        $cc = CurriculumCourse::findOrFail($id);
        $this->editId = $id;

        $this->curriculum_id = $cc->curriculum_id;
        $this->course_id = $cc->course_id;
        $this->semester = $cc->semester;
        $this->course_group_id = $cc->course_group_id;
        $this->is_mandatory = (bool) $cc->is_mandatory;
        $this->credit_total = $cc->credit_total;
        $this->credit_theory = $cc->credit_theory;
        $this->credit_practice = $cc->credit_practice;

        $this->isModalOpen = true;
        $this->isEditMode = true;
    }

    public function save()
    {
        $validatedData = $this->validate();
        // Bersihkan nilai null pada integer
        $validatedData['credit_theory'] = $validatedData['credit_theory'] ?? 0;
        $validatedData['credit_practice'] = $validatedData['credit_practice'] ?? 0;

        if ($this->isEditMode && $this->editId) {
            // UPDATE
            $cc = CurriculumCourse::findOrFail($this->editId);
            $cc->update($validatedData);

            session()->flash('message', 'Course berhasil diperbarui.');
        } else {
            $validatedData['id'] = (string) Str::ulid();

            CurriculumCourse::create($validatedData);

            session()->flash('message', 'Course berhasil ditambahkan ke kurikulum.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        $cc = CurriculumCourse::findOrFail($id);
        $cc->delete();
        session()->flash('message', 'Course berhasil dihapus dari kurikulum.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->reset([
            'curriculum_id',
            'course_id',
            'semester',
            'course_group_id',
            'is_mandatory',
            'credit_total',
            'credit_theory',
            'credit_practice',
            'editId',
            'isEditMode'
        ]);

        // Reset ke default MKK lagi jika create baru
        $defaultGroup = CourseGroup::where('code', 'MKK')->first();
        $this->course_group_id = $defaultGroup->id ?? null;
    }
}
