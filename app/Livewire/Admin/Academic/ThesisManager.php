<?php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Thesis;
use App\Models\ThesisSupervisor;
use App\Models\Lecturer;
use App\Models\StudyProgram;

class ThesisManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_prodi = '';
    public $filter_status = 'PROPOSED'; // Default tampilkan yg baru mengajukan

    // Modal State
    public $isModalOpen = false;
    public $selectedThesis;
    
    // Form Plotting
    public $supervisor1_id;
    public $supervisor2_id;
    public $rejection_note; // Nanti bisa dikirim ke mahasiswa (perlu kolom notes di db thesis jika mau permanen, tp skrg flash message dulu)

    public function render()
    {
        $theses = Thesis::with(['student.user', 'student.study_program'])
            ->when($this->search, function($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                  ->orWhereHas('student.user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filter_prodi, fn($q) => $q->whereHas('student', fn($s) => $s->where('study_program_id', $this->filter_prodi)))
            ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.academic.thesis-manager', [
            'theses' => $theses,
            'prodis' => StudyProgram::all(),
            'lecturers' => Lecturer::with('user')->get()
        ])->layout('layouts.admin');
    }

    public function showDetail($id)
    {
        $this->selectedThesis = Thesis::with(['student', 'supervisors'])->find($id);
        
        // Load existing supervisors if any
        $p1 = $this->selectedThesis->supervisors->where('role', 1)->first();
        $p2 = $this->selectedThesis->supervisors->where('role', 2)->first();
        
        $this->supervisor1_id = $p1 ? $p1->lecturer_id : null;
        $this->supervisor2_id = $p2 ? $p2->lecturer_id : null;

        $this->isModalOpen = true;
    }

    public function approve()
    {
        $this->validate([
            'supervisor1_id' => 'required|exists:lecturers,id',
            'supervisor2_id' => 'nullable|exists:lecturers,id|different:supervisor1_id',
        ]);

        // 1. Update Status Skripsi
        $this->selectedThesis->update(['status' => 'APPROVED']); // Status berubah jadi ON_PROGRESS atau APPROVED (tergantung flow, kita pakai APPROVED sbg tanda acc proposal)

        // 2. Simpan Pembimbing (Reset dulu biar bersih)
        $this->selectedThesis->supervisors()->delete();

        // Pembimbing 1
        ThesisSupervisor::create([
            'thesis_id' => $this->selectedThesis->id,
            'lecturer_id' => $this->supervisor1_id,
            'role' => 1,
            'status' => 'ACCEPTED' 
        ]);

        // Pembimbing 2 (Opsional)
        if ($this->selectedThesis->id && $this->supervisor2_id) {
            ThesisSupervisor::create([
                'thesis_id' => $this->selectedThesis->id,
                'lecturer_id' => $this->supervisor2_id,
                'role' => 2,
                'status' => 'ACCEPTED'
            ]);
        }

        session()->flash('message', 'Judul disetujui & Pembimbing berhasil di-plot.');
        $this->isModalOpen = false;
    }

    public function reject()
    {
        // Set status REJECTED agar mahasiswa bisa edit judul lagi
        $this->selectedThesis->update(['status' => 'REJECTED']);
        
        session()->flash('message', 'Pengajuan judul ditolak. Mahasiswa diminta revisi.');
        $this->isModalOpen = false;
    }
}