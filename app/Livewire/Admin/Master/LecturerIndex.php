<?php

namespace App\Livewire\Admin\Master;

use App\Models\User;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LecturerIndex extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    
    // SMART FILTERS
    public $filter_prodi = '';
    public $filter_status = ''; // '' (Semua), '1' (Aktif), '0' (Non-Aktif)
    public $filter_category = ''; // '' (Semua), 'nidn' (Ber-NIDN), 'non_nidn' (Tanpa NIDN)
    
    // State Modal
    public $isModalOpen = false;
    public $isEditMode = false;
    public $isImportModalOpen = false;

    // Form Properties
    public $lecturer_id, $user_id;
    public $name, $email, $password;
    
    // Data Spesifik Dosen
    public $nidn, $nip_internal;
    public $front_title, $back_title;
    public $phone, $study_program_id;
    public $is_active = true; // Default aktif saat create

    // Property Import
    public $file_import;

    // Statistik Ringkasan (Smart Dashboard)
    public $summary = [
        'total' => 0,
        'active' => 0,
        'nidn' => 0,
        'inactive' => 0
    ];

    // --- RESET PAGINATION SAAT FILTER BERUBAH ---
    public function updatedSearch() { $this->resetPage(); }
    public function updatedFilterProdi() { $this->resetPage(); }
    public function updatedFilterStatus() { $this->resetPage(); }
    public function updatedFilterCategory() { $this->resetPage(); }
    // --------------------------------------------

    public function render()
    {
        // 1. Query Dasar
        $query = Lecturer::with(['user', 'study_program'])
            ->where(function($q) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhere('nidn', 'like', '%'.$this->search.'%')
                  ->orWhere('nip_internal', 'like', '%'.$this->search.'%');
            })
            // Filter Prodi
            ->when($this->filter_prodi, fn($q) => $q->where('study_program_id', $this->filter_prodi))
            // Filter Status (Smart)
            ->when($this->filter_status !== '', function($q) {
                $q->where('is_active', $this->filter_status == '1');
            })
            // Filter Kategori (Smart)
            ->when($this->filter_category, function($q) {
                if ($this->filter_category == 'nidn') {
                    $q->whereNotNull('nidn')->where('nidn', '!=', '');
                } elseif ($this->filter_category == 'non_nidn') {
                    $q->where(fn($sub) => $sub->whereNull('nidn')->orWhere('nidn', ''));
                }
            });

        // 2. Hitung Statistik Real-time (Sebelum Paginasi)
        // Kita hitung global (tanpa filter prodi/search) agar dashboard tetap informatif
        $this->summary['total'] = Lecturer::count();
        $this->summary['active'] = Lecturer::where('is_active', true)->count();
        $this->summary['inactive'] = Lecturer::where('is_active', false)->count();
        $this->summary['nidn'] = Lecturer::whereNotNull('nidn')->where('nidn', '!=', '')->count();

        // 3. Ambil Data
        $lecturers = $query->latest()->paginate(10);

        return view('livewire.admin.master.lecturer-index', [
            'lecturers' => $lecturers,
            'prodis' => StudyProgram::all()
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->is_active = true; // Default checked
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

        // Data Lecturer
        $this->nidn = $lecturer->nidn;
        $this->nip_internal = $lecturer->nip_internal;
        $this->front_title = $lecturer->front_title;
        $this->back_title = $lecturer->back_title;
        $this->phone = $lecturer->phone;
        $this->study_program_id = $lecturer->study_program_id;
        $this->is_active = (bool) $lecturer->is_active;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user_id)],
            'nidn' => ['nullable', Rule::unique('lecturers', 'nidn')->ignore($this->lecturer_id)],
            'nip_internal' => ['nullable', Rule::unique('lecturers', 'nip_internal')->ignore($this->lecturer_id)],
            'study_program_id' => 'required|exists:study_programs,id',
            'phone' => 'nullable|numeric',
            'is_active' => 'boolean'
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
                    'username' => $this->nidn ?? $this->nip_internal ?? explode('@', $this->email)[0],
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
                    'is_active' => $this->is_active,
                ]);

                session()->flash('message', 'Data Dosen diperbarui.');

            } else {
                // CREATE
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'username' => $this->nidn ?? $this->nip_internal ?? explode('@', $this->email)[0],
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
                    'is_active' => $this->is_active,
                ]);

                session()->flash('message', 'Dosen berhasil ditambahkan.');
            }
        });

        $this->closeModal();
    }

    // --- IMPORT FEATURE ---
    public function openImportModal()
    {
        $this->resetErrorBag();
        $this->file_import = null;
        $this->isImportModalOpen = true;
    }

    public function closeImportModal()
    {
        $this->isImportModalOpen = false;
        $this->file_import = null;
    }

    public function import()
    {
        $this->validate([
            'file_import' => 'required|mimes:csv,txt|max:2048',
        ]);

        $path = $this->file_import->getRealPath();
        $handle = fopen($path, 'r');
        
        // Skip Header
        fgetcsv($handle);

        DB::beginTransaction();
        try {
            $count = 0;
            while (($row = fgetcsv($handle)) !== FALSE) {
                if(count($row) < 2) continue;

                $name = $row[0];
                $email = $row[1];
                $nidn = $row[2] ?? null;
                $nip = $row[3] ?? null;
                $prodiCode = $row[4] ?? null;

                if (User::where('email', $email)->exists()) continue;

                $prodi = StudyProgram::where('code', $prodiCode)->first();
                $prodiId = $prodi ? $prodi->id : null;

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'username' => $nidn ?? $nip ?? explode('@', $email)[0],
                    'password' => Hash::make('password'), 
                    'role' => 'lecturer',
                    'email_verified_at' => now(),
                ]);

                Lecturer::create([
                    'user_id' => $user->id,
                    'nidn' => $nidn,
                    'nip_internal' => $nip,
                    'study_program_id' => $prodiId,
                    'is_active' => true,
                ]);
                $count++;
            }
            
            DB::commit();
            fclose($handle);
            
            session()->flash('message', "Berhasil mengimport $count data dosen.");
            $this->closeImportModal();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('file_import', 'Gagal Import: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $lecturer = Lecturer::find($id);
        if ($lecturer) {
            if ($lecturer->user) $lecturer->user->delete();
            $lecturer->delete();
            session()->flash('message', 'Data Dosen dihapus.');
        }
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->reset([
            'lecturer_id', 'user_id', 'name', 'email', 'password',
            'nidn', 'nip_internal', 'front_title', 'back_title', 'phone', 'study_program_id',
            'is_active'
        ]);
        $this->resetErrorBag();
    }
}