<?php

namespace App\Livewire\Student\Letter;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\LetterRequest;
use Livewire\WithPagination;

class RequestIndex extends Component
{
    use WithPagination;

    public $type = 'AKTIF_KULIAH';
    public $purpose;
    
    public $isModalOpen = false;

    public function render()
    {
        $student = Auth::user()->student;
        
        $requests = LetterRequest::where('student_id', $student->id)
            ->latest()
            ->paginate(5);

        return view('livewire.student.letter.request-index', [
            'requests' => $requests
        ])->layout('layouts.student');
    }

    public function store()
    {
        $this->validate([
            'type' => 'required',
            'purpose' => 'required|string|min:10',
        ]);

        LetterRequest::create([
            'student_id' => Auth::user()->student->id,
            'type' => $this->type,
            'purpose' => $this->purpose,
            'status' => 'PENDING'
        ]);

        session()->flash('message', 'Permohonan surat berhasil dikirim. Tunggu verifikasi BAAK.');
        $this->isModalOpen = false;
        $this->reset(['purpose']);
    }
}