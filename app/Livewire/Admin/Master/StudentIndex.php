<?php

namespace App\Livewire\Admin\Master;

use App\Models\Lecturer;
use App\Models\User;
use App\Models\Student;
use App\Models\StudyProgram;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;

    // Form Properties (LENGKAP)
    public $student_id, $user_id;
    public $name, $email, $password; // Data User
    public $nim, $prodi_id, $entry_year, $status; // Data Akademik
    public $pob, $dob, $gender, $phone, $address; // Biodata
    public $academic_advisor_id;


    public function render()
    {
       $students = Student::with(['user', 'study_program', 'academic_advisor.user']) // Load advisor
            ->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orWhere('nim', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.master.student-index', [
            'students' => $students,
            'prodis' => StudyProgram::all(),
            'lecturers' => Lecturer::with('user')->get()
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->entry_year = date('Y'); // Default tahun ini
        $this->status = 'A'; // Default Aktif
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $student = Student::with('user')->find($id);

        $this->student_id = $id;
        $this->user_id = $student->user_id;

        // Data User
        $this->name = $student->user->name;
        $this->email = $student->user->email;
        $this->password = '';

        // Data Student
        $this->nim = $student->nim;
        $this->prodi_id = $student->study_program_id;
        $this->entry_year = $student->entry_year;
        $this->status = $student->status;
        $this->pob = $student->pob;
        $this->dob = $student->dob;
        $this->gender = $student->gender;
        $this->phone = $student->phone;
        $this->address = $student->address;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
            'nim' => ['required', Rule::unique('students', 'nim')->ignore($this->student_id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user_id)],
            'prodi_id' => 'required',
            'entry_year' => 'required|numeric|digits:4',
            'gender' => 'required|in:L,P',
            'status' => 'required|in:A,C,D,L,N', // Aktif, Cuti, DO, Lulus, Non-aktif
        ];

        // Validasi password hanya jika create atau jika diisi saat edit
        if (!$this->isEditMode) {
            $rules['password'] = 'required|min:6';
        } else {
            $rules['password'] = 'nullable|min:6';
        }

        $this->validate($rules);

        DB::transaction(function () {

            if ($this->isEditMode) {
                // UPDATE
                $user = User::find($this->user_id);
                $userData = [
                    'name' => $this->name,
                    'email' => $this->email,
                    'username' => $this->nim,
                ];
                if (!empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                }
                $user->update($userData);

                Student::where('id', $this->student_id)->update([
                    'study_program_id' => $this->prodi_id,
                    'nim' => $this->nim,
                    'entry_year' => $this->entry_year,
                    'pob' => $this->pob,
                    'dob' => $this->dob,
                    'gender' => $this->gender,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'status' => $this->status,
                    'academic_advisor_id' => $this->academic_advisor_id,
                ]);

                session()->flash('message', 'Data Mahasiswa diperbarui.');
            } else {
                // CREATE
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'username' => $this->nim,
                    'password' => Hash::make($this->password),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]);

                Student::create([
                    'user_id' => $user->id,
                    'study_program_id' => $this->prodi_id,
                    'nim' => $this->nim,
                    'entry_year' => $this->entry_year,
                    'pob' => $this->pob,
                    'dob' => $this->dob,
                    'gender' => $this->gender,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'status' => $this->status,
                    'academic_advisor_id' => $this->academic_advisor_id,
                ]);

                session()->flash('message', 'Mahasiswa berhasil didaftarkan.');
            }
        });

        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function delete($id)
    {
        $s = Student::find($id);
        if ($s->user) $s->user->delete();
        $s->delete();
        session()->flash('message', 'Data Mahasiswa dihapus.');
    }

    private function resetFields()
    {
        $this->reset([
            'student_id',
            'user_id',
            'name',
            'email',
            'password',
            'nim',
            'prodi_id',
            'entry_year',
            'status',
            'pob',
            'dob',
            'gender',
            'phone',
            'address',
            'academic_advisor_id'
        ]);
    }
}
