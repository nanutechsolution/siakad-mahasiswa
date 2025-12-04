<?php

namespace App\Livewire\Student\Thesis;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Thesis;
use App\Models\AcademicPeriod;

class ThesisProposal extends Component
{
    use WithFileUploads;

    public $student;
    public $thesis; // Data skripsi yang sudah ada (jika ada)

    // Form Fields
    public $title;
    public $abstract;
    public $file; // Temporary file upload
    public $existing_file; // Path file lama

    public function mount()
    {
        $this->student = Auth::user()->student;

        if (!$this->student) {
            abort(403, 'Data mahasiswa tidak ditemukan.');
        }

        // Cek apakah mahasiswa sudah punya pengajuan skripsi
        $this->thesis = Thesis::where('student_id', $this->student->id)->first();

        if ($this->thesis) {
            $this->title = $this->thesis->title;
            $this->abstract = $this->thesis->abstract;
            $this->existing_file = $this->thesis->proposal_file;
        }
    }

    public function save()
    {
        $this->validate([
            'title' => 'required|string|min:10|max:255',
            'abstract' => 'required|string|min:50',
            // File wajib jika buat baru, opsional jika edit (karena bisa pakai file lama)
            'file' => $this->thesis ? 'nullable|mimes:pdf|max:5120' : 'required|mimes:pdf|max:5120', // Max 5MB
        ]);

        $activePeriod = AcademicPeriod::where('is_active', true)->first();

        if (!$activePeriod) {
            session()->flash('error', 'Tidak ada periode akademik aktif. Hubungi BAAK.');
            return;
        }

        // 1. Handle File Upload
        $filePath = $this->existing_file;
        if ($this->file) {
            // Hapus file lama jika ada
            if ($this->existing_file && Storage::disk('public')->exists($this->existing_file)) {
                Storage::disk('public')->delete($this->existing_file);
            }
            $filePath = $this->file->store('thesis-proposals', 'public');
        }

        // 2. Simpan ke Database (Update or Create)
        $this->thesis = Thesis::updateOrCreate(
            ['student_id' => $this->student->id],
            [
                'academic_period_id' => $activePeriod->id,
                'title' => $this->title,
                'abstract' => $this->abstract,
                'proposal_file' => $filePath,
                // Jika statusnya REJECTED, reset jadi PROPOSED agar direview ulang
                'status' => ($this->thesis && $this->thesis->status == 'REJECTED') ? 'PROPOSED' : ($this->thesis->status ?? 'PROPOSED')
            ]
        );

        // Reset input file
        $this->file = null;
        $this->existing_file = $filePath;

        session()->flash('success', 'Proposal Skripsi berhasil diajukan! Menunggu persetujuan Kaprodi.');
    }

    public function render()
    {
        return view('livewire.student.thesis.thesis-proposal')->layout('layouts.student');
    }
}