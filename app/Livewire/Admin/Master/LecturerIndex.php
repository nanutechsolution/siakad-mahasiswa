<?php
namespace App\Livewire\Admin\Master;

use App\Models\User;
use App\Models\Lecturer;
use App\Models\StudyProgram; // Load Model Prodi
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LecturerIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;
    
    // Form Properties (LENGKAP)
    public $lecturer_id, $user_id;
    public $name, $email, $password;
    
    // Data Spesifik Dosen
    public $nidn, $nip_internal;
    public $front_title, $back_title;
    public $phone, $study_program_id;

    public function render()
    {
        $lecturers = Lecturer::with(['user', 'study_program'])
            ->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orWhere('nidn', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate(10);

        return view('livewire.admin.master.lecturer-index', [
            'lecturers' => $lecturers,
            'prodis' => StudyProgram::all() // Kirim data prodi ke view
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $lecturer = Lecturer::with('user')->find($id);
        
        $this->lecturer_id = $id;
        $this->user_id = $lecturer->user_id;
        
        // Data User
        $this->name = $lecturer->user->name;
        $this->email = $lecturer->user->email;
        $this->password = ''; 

        // Data Lecturer Lengkap
        $this->nidn = $lecturer->nidn;
        $this->nip_internal = $lecturer->nip_internal;
        $this->front_title = $lecturer->front_title;
        $this->back_title = $lecturer->back_title;
        $this->phone = $lecturer->phone;
        $this->study_program_id = $lecturer->study_program_id;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        // 1. Validasi
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user_id)],
            'nidn' => ['nullable', Rule::unique('lecturers', 'nidn')->ignore($this->lecturer_id)],
            'nip_internal' => ['nullable', Rule::unique('lecturers', 'nip_internal')->ignore($this->lecturer_id)],
            'study_program_id' => 'required|exists:study_programs,id',
            'phone' => 'nullable|numeric',
        ];

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
                    'username' => $this->nidn ?? $this->nip_internal, // Username prioritas NIDN lalu NIP
                ];
                if (!empty($this->password)) {
                    $userData['password'] = Hash::make($this->password);
                }
                $user->update($userData);

                Lecturer::where('id', $this->lecturer_id)->update([
                    'nidn' => $this->nidn,
                    'nip_internal' => $this->nip_internal,
                    'front_title' => $this->front_title,
                    'back_title' => $this->back_title,
                    'phone' => $this->phone,
                    'study_program_id' => $this->study_program_id,
                ]);

                session()->flash('message', 'Data Dosen diperbarui.');

            } else {
                // CREATE
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'username' => $this->nidn ?? $this->nip_internal,
                    'password' => Hash::make($this->password),
                    'role' => 'lecturer',
                    'email_verified_at' => now(),
                ]);

                Lecturer::create([
                    'user_id' => $user->id,
                    'nidn' => $this->nidn,
                    'nip_internal' => $this->nip_internal,
                    'front_title' => $this->front_title,
                    'back_title' => $this->back_title,
                    'phone' => $this->phone,
                    'study_program_id' => $this->study_program_id,
                    'is_active' => true,
                ]);

                session()->flash('message', 'Dosen berhasil ditambahkan.');
            }
        });

        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function delete($id)
    {
        $lecturer = Lecturer::find($id);
        if ($lecturer->user) $lecturer->user->delete();
        $lecturer->delete();
        session()->flash('message', 'Data Dosen dihapus.');
    }

    private function resetFields()
    {
        $this->reset([
            'lecturer_id', 'user_id', 'name', 'email', 'password',
            'nidn', 'nip_internal', 'front_title', 'back_title', 'phone', 'study_program_id'
        ]);
    }
}