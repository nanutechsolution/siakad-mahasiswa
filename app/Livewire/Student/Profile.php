<?php

namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Profile extends Component
{
    use WithFileUploads;

    public $student;
    public $user;

    // Foto
    public $photo;
    public $existing_photo;

    // 1. Data Akun & Kontak
    public $email, $phone;

    // 2. Data Pribadi (Sesuai Feeder)
    public $nik, $nisn, $npwp, $pob, $dob, $gender, $religion_id, $citizenship;

    // 3. Alamat Domisili
    public $address, $dusun, $rt, $rw, $kelurahan, $postal_code;

    // 4. Data Orang Tua
    public $father_nik, $father_name, $mother_nik, $mother_name;

    public function mount()
    {
        $this->user = Auth::user();
        $this->student = $this->user->student;

        if (!$this->student) abort(403);

        // Load Data Akun
        $this->email = $this->user->email;
        $this->existing_photo = $this->student->photo;

        // Load Data Student ke Property
        $this->phone = $this->student->phone;
        
        $this->nik = $this->student->nik;
        $this->nisn = $this->student->nisn;
        $this->npwp = $this->student->npwp;
        $this->pob = $this->student->pob;
        $this->dob = $this->student->dob ? $this->student->dob->format('Y-m-d') : null;
        $this->gender = $this->student->gender;
        $this->religion_id = $this->student->religion_id;
        $this->citizenship = $this->student->citizenship ?? 'ID';

        $this->address = $this->student->address;
        $this->dusun = $this->student->dusun;
        $this->rt = $this->student->rt;
        $this->rw = $this->student->rw;
        $this->kelurahan = $this->student->kelurahan;
        $this->postal_code = $this->student->postal_code;

        $this->father_nik = $this->student->father_nik;
        $this->father_name = $this->student->father_name;
        $this->mother_nik = $this->student->mother_nik;
        $this->mother_name = $this->student->mother_name;
    }

    public function update()
    {
        $this->validate([
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user->id)],
            'phone' => 'required|numeric',
            'nik' => 'required|numeric|digits:16',
            'nisn' => 'nullable|numeric',
            'npwp' => 'nullable|numeric',
            'pob' => 'required|string',
            'dob' => 'required|date',
            'gender' => 'required|in:L,P',
            'religion_id' => 'required|integer',
            'mother_name' => 'required|string', // Wajib untuk Feeder
        ]);

        // 1. Upload Foto
        if ($this->photo) {
            if ($this->existing_photo) {
                Storage::disk('public')->delete($this->existing_photo);
            }
            $path = $this->photo->store('profile-photos', 'public');
            $this->student->update(['photo' => $path]);
            $this->existing_photo = $path;
        }

        // 2. Update User
        $this->user->update(['email' => $this->email]);

        // 3. Update Student (Semua Field)
        $this->student->update([
            'phone' => $this->phone,
            'nik' => $this->nik,
            'nisn' => $this->nisn,
            'npwp' => $this->npwp,
            'pob' => $this->pob,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'religion_id' => $this->religion_id,
            'citizenship' => $this->citizenship,
            
            'address' => $this->address,
            'dusun' => $this->dusun,
            'rt' => $this->rt,
            'rw' => $this->rw,
            'kelurahan' => $this->kelurahan,
            'postal_code' => $this->postal_code,

            'father_nik' => $this->father_nik,
            'father_name' => $this->father_name,
            'mother_nik' => $this->mother_nik,
            'mother_name' => $this->mother_name,
        ]);

        $this->photo = null;
        session()->flash('message', 'Biodata berhasil diperbarui sesuai standar PDDIKTI.');
        
        // Trigger event browser agar scroll ke atas
        $this->dispatch('profile-updated');
    }

    public function render()
    {
        return view('livewire.student.profile')->layout('layouts.student');
    }
}