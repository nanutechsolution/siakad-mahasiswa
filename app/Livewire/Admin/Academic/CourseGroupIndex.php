<?php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CourseGroup;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CourseGroupIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;
    public $editId = null;

    // Form Fields
    public $code;
    public $name;
    public $description;

    protected function rules()
    {
        return [
            'code' => [
                'required', 'string', 'max:20',
                Rule::unique('course_groups', 'code')->ignore($this->editId)
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = CourseGroup::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
            });
        }

        return view('livewire.admin.academic.course-group-index', [
            'groups' => $query->orderBy('code')->paginate(10)
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetForm();
        $this->isModalOpen = true;
        $this->isEditMode = false;
    }

    public function edit($id)
    {
        $group = CourseGroup::findOrFail($id);
        $this->editId = $id;
        $this->code = $group->code;
        $this->name = $group->name;
        $this->description = $group->description;

        $this->isModalOpen = true;
        $this->isEditMode = true;
    }

    public function save()
    {
        $validated = $this->validate();

        if ($this->isEditMode) {
            CourseGroup::findOrFail($this->editId)->update($validated);
            session()->flash('message', 'Group berhasil diperbarui.');
        } else {
            // ID otomatis diisi oleh Model (Booted)
            CourseGroup::create($validated);
            session()->flash('message', 'Group berhasil ditambahkan.');
        }

        $this->closeModal();
    }

    public function delete($id)
    {
        // Cek apakah dipakai di tabel lain (Opsional, tapi disarankan)
        // if (CurriculumCourse::where('course_group_id', $id)->exists()) {
        //    session()->flash('error', 'Group tidak bisa dihapus karena sedang digunakan.');
        //    return;
        // }

        CourseGroup::findOrFail($id)->delete();
        session()->flash('message', 'Group berhasil dihapus.');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->reset(['code', 'name', 'description', 'editId', 'isEditMode']);
    }
}