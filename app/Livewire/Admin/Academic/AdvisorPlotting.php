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
    public $show_has_advisor = false; // false = Tampilkan yg belum punya PA saja

    // Action State
    public $selected_students = []; // Array ID mahasiswa yang dicentang
    public $selected_lecturer;      // ID Dosen yang dipilih
    public $select_all = false;     // Checkbox select all

    public function mount()
    {
        $this->filter_angkatan = date('Y');
        // Default pilih prodi pertama biar data langsung muncul
        $this->filter_prodi = StudyProgram::first()->id ?? null;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Ambil semua ID yang sesuai filter saat ini
            $this->selected_students = $this->getStudentsQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected_students = [];
        }
    }

    public function getStudentsQuery()
    {
        return Student::with(['user', 'academic_advisor.user'])
            ->when($this->filter_prodi, fn($q) => $q->where('study_program_id', $this->filter_prodi))
            ->when($this->filter_angkatan, fn($q) => $q->where('entry_year', $this->filter_angkatan))
            ->when(!$this->show_has_advisor, fn($q) => $q->whereNull('academic_advisor_id')) // Filter Null
            ->orderBy('nim');
    }

    public function save()
    {
        $this->validate([
            'selected_students' => 'required|array|min:1',
            'selected_lecturer' => 'required|exists:lecturers,id',
        ]);

        // Update Massal
        Student::whereIn('id', $this->selected_students)
            ->update(['academic_advisor_id' => $this->selected_lecturer]);

        $count = count($this->selected_students);
        $dosen = Lecturer::with('user')->find($this->selected_lecturer);
        
        session()->flash('message', "Berhasil! $count mahasiswa kini dibimbing oleh " . $dosen->user->name);
        
        // Reset
        $this->selected_students = [];
        $this->select_all = false;
    }

    // Fitur Lepas PA (Reset jadi Null)
    public function detach()
    {
        $this->validate(['selected_students' => 'required|array|min:1']);

        Student::whereIn('id', $this->selected_students)
            ->update(['academic_advisor_id' => null]);

        session()->flash('message', 'Dosen Wali berhasil dilepas dari mahasiswa terpilih.');
        $this->selected_students = [];
        $this->select_all = false;
    }

    public function render()
    {
        $students = $this->getStudentsQuery()->paginate(50); // Tampilkan banyak biar gampang checklist

        // Ambil dosen yang sesuai prodi terpilih (opsional, atau semua dosen)
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
