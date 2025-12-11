<?php
namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LetterRequest;

class LetterManager extends Component
{
    use WithPagination;

    public $filter_status = 'PENDING';
    
    // Modal Process
    public $isModalOpen = false;
    public $selectedRequest;
    public $letter_number;
    public $admin_note;

    public function render()
    {
        $requests = LetterRequest::with('student.user')
            ->where('status', $this->filter_status)
            ->latest()
            ->paginate(10);

        return view('livewire.admin.system.letter-manager', [
            'requests' => $requests
        ])->layout('layouts.admin');
    }

    public function process($id)
    {
        $this->selectedRequest = LetterRequest::with('student')->find($id);
        $this->letter_number = ''; // Bisa generate otomatis format nomor surat disini
        $this->admin_note = '';
        $this->isModalOpen = true;
    }

    public function approve()
    {
        $this->validate(['letter_number' => 'required']);

        $this->selectedRequest->update([
            'status' => 'COMPLETED',
            'letter_number' => $this->letter_number,
            'admin_note' => $this->admin_note
        ]);

        session()->flash('message', 'Surat diterbitkan.');
        $this->isModalOpen = false;
    }

    public function reject()
    {
        $this->validate(['admin_note' => 'required']);

        $this->selectedRequest->update([
            'status' => 'REJECTED',
            'admin_note' => $this->admin_note
        ]);

        session()->flash('error', 'Permohonan ditolak.');
        $this->isModalOpen = false;
    }
}