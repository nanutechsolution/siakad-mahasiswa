<?php
namespace App\Livewire\Student;

use Livewire\Component;
use Livewire\WithFileUploads; // Wajib untuk upload
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Profile extends Component
{
    use WithFileUploads;

    public $student;
    public $user;

    // Form Fields
    public $email, $phone, $address, $pob, $dob;
    public $photo; // Temporary file upload
    public $existing_photo; // Path foto lama

    public function mount()
    {
        $this->user = Auth::user();
        $this->student = $this->user->student;

        // Isi form dengan data database
        $this->email = $this->user->email;
        $this->phone = $this->student->phone;
        $this->address = $this->student->address;
        $this->pob = $this->student->pob;
        $this->dob = $this->student->dob;
        $this->existing_photo = $this->student->photo;
    }

    public function update()
    {
        $this->validate([
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'phone' => 'required|numeric',
            'address' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:1024', // Max 1MB
        ]);

        // 1. Handle Upload Foto
        if ($this->photo) {
            // Hapus foto lama jika ada (optional, biar hemat storage)
            if ($this->existing_photo) {
                Storage::disk('public')->delete($this->existing_photo);
            }
            // Simpan foto baru
            $path = $this->photo->store('profile-photos', 'public');
            $this->student->update(['photo' => $path]);
            $this->existing_photo = $path; // Update tampilan
        }

        // 2. Update Data User (Email)
        $this->user->update([
            'email' => $this->email
        ]);

        // 3. Update Data Student
        $this->student->update([
            'phone' => $this->phone,
            'address' => $this->address,
            'pob' => $this->pob,
            'dob' => $this->dob,
        ]);

        // Reset input file
        $this->photo = null;

        session()->flash('message', 'Profil berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.student.profile')->layout('layouts.student');
    }
}