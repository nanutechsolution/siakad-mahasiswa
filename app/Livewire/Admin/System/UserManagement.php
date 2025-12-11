<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_role = '';
    
    // Modal State
    public $isModalOpen = false;
    public $isEditMode = false;

    // Form
    public $user_id, $name, $email, $role, $password;

    public function render()
    {
        $users = User::query()
            ->when($this->search, function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%')
                  ->orWhere('username', 'like', '%'.$this->search.'%');
            })
            ->when($this->filter_role, fn($q) => $q->where('role', $this->filter_role))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.system.user-management', [
            'users' => $users
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
        $user = User::find($id);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = ''; // Kosongkan
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->user_id,
            'role' => 'required|in:admin,lecturer,student,camaba',
        ];

        if (!$this->isEditMode) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        // Jika Create baru, username default email (nanti diupdate sistem lain)
        if (!$this->isEditMode) {
            $data['username'] = $this->email;
            $data['email_verified_at'] = now();
        }

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->user_id], $data);

        session()->flash('message', 'User berhasil disimpan.');
        $this->isModalOpen = false;
    }

    // FITUR: RESET PASSWORD CEPAT
    public function resetPassword($id)
    {
        $user = User::find($id);
        // Default password reset jadi '12345678'
        $user->update(['password' => Hash::make('12345678')]);
        
        session()->flash('message', "Password untuk {$user->name} di-reset menjadi: 12345678");
    }

    // FITUR: BLOKIR / BUKA BLOKIR
    public function toggleStatus($id)
    {
        $user = User::find($id);
        // Kita asumsikan ada kolom is_active di users (jika belum, harus migrasi)
        // Cek struktur tabel users Anda, biasanya di Laravel Breeze ada atau kita buat sebelumnya
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'Diaktifkan' : 'Diblokir';
        session()->flash('message', "User {$user->name} berhasil $status.");
    }

    public function delete($id)
    {
        $user = User::find($id);
        if ($user->role == 'admin' && User::where('role', 'admin')->count() == 1) {
            session()->flash('error', 'Tidak bisa menghapus Admin terakhir!');
            return;
        }
        $user->delete();
        session()->flash('message', 'User dihapus.');
    }

    private function resetFields()
    {
        $this->reset(['user_id', 'name', 'email', 'role', 'password']);
    }
}