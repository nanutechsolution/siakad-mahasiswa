<?php

namespace App\Livewire\Lecturer\Thesis;

use Livewire\Component;
use App\Models\Thesis;
use App\Models\ThesisLog;
use Illuminate\Support\Facades\Auth;

class GuidanceDetail extends Component
{
    public $thesis;
    public $logs;

    // Form Validasi
    public $log_id;
    public $lecturer_notes;

    public $isModalOpen = false;

    public function mount($thesisId)
    {
        // Pastikan dosen berhak akses (Security Check)
        $lecturerId = Auth::user()->lecturer->id;
        
        $this->thesis = Thesis::whereHas('supervisors', function($q) use ($lecturerId) {
            $q->where('lecturer_id', $lecturerId);
        })->with(['student.user', 'logs' => function($q) {
            $q->orderBy('guidance_date', 'desc');
        }])->findOrFail($thesisId);
        
        $this->logs = $this->thesis->logs;
    }

    public function validateLog($logId)
    {
        $log = ThesisLog::find($logId);
        $this->log_id = $logId;
        $this->lecturer_notes = $log->notes; // Load catatan lama jika ada
        $this->isModalOpen = true;
    }

    public function saveValidation()
    {
        $this->validate([
            'lecturer_notes' => 'required|string|min:5'
        ]);

        ThesisLog::where('id', $this->log_id)->update([
            'notes' => $this->lecturer_notes,
            'status' => 'APPROVED' // Menandakan bimbingan ini sah/di-acc
        ]);

        session()->flash('message', 'Bimbingan berhasil divalidasi.');
        $this->isModalOpen = false;
        
        // Refresh data
        $this->mount($this->thesis->id);
    }

    public function render()
    {
        return view('livewire.lecturer.thesis.guidance-detail')->layout('layouts.lecturer');
    }
}