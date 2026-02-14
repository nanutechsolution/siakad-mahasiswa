<?php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\StudyProgram;

class AdvisorPlotting extends Component
{
    use WithPagination;

    // Filter Data
    public $filter_prodi;
    public $filter_angkatan;
    public $show_has_advisor = false; 

    // Action State
    public $selected_students = []; 
    public $selected_lecturer;      
    public $select_all = false;     

    public function mount()
    {
        $this->filter_angkatan = date('Y');
        $this->filter_prodi = StudyProgram::first()->id ?? null;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected_students = $this->getStudentsQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected_students = [];
        }
    }

    public function getStudentsQuery()
    {
        // Menggunakan relasi academicAdvisor (camelCase)
        return Student::with(['user', 'academicAdvisor.user'])
            ->when($this->filter_prodi, fn($q) => $q->where('study_program_id', $this->filter_prodi))
            ->when($this->filter_angkatan, fn($q) => $q->where('entry_year', $this->filter_angkatan))
            ->when(!$this->show_has_advisor, fn($q) => $q->whereNull('academic_advisor_id'))
            ->orderBy('nim');
    }

    public function save()
    {
        $this->validate([
            'selected_students' => 'required|array|min:1',
            'selected_lecturer' => 'required|exists:lecturers,id',
        ]);

        Student::whereIn('id', $this->selected_students)
            ->update(['academic_advisor_id' => $this->selected_lecturer]);

        $count = count($this->selected_students);
        $dosen = Lecturer::with('user')->find($this->selected_lecturer);
        
        session()->flash('message', "Berhasil! $count mahasiswa di-plotting ke " . ($dosen->user->name ?? 'Dosen'));
        
        $this->reset(['selected_students', 'select_all']);
    }

    public function detach()
    {
        $this->validate(['selected_students' => 'required|array|min:1']);

        Student::whereIn('id', $this->selected_students)
            ->update(['academic_advisor_id' => null]);

        session()->flash('message', 'Dosen Wali berhasil dilepas.');
        $this->reset(['selected_students', 'select_all']);
    }

    public function render()
    {
        $students = $this->getStudentsQuery()->paginate(100);

        $lecturers = Lecturer::with('user')
            ->when($this->filter_prodi, fn($q) => $q->where('study_program_id', $this->filter_prodi))
            ->get()
            ->sortBy('user.name');

        return view('livewire.admin.academic.advisor-plotting', [
            'students' => $students,
            'lecturers' => $lecturers,
            'prodis' => StudyProgram::all()
        ])->layout('layouts.admin');
    }
}