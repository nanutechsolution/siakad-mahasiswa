<?php

namespace App\Livewire\Student\Thesis;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\Thesis;
use App\Models\ThesisLog;

class ThesisLogIndex extends Component
{
    use WithFileUploads;

    public $thesis;
    public $logs;

    // Form Fields
    public $guidance_date;
    public $progress_report; // Laporan mahasiswa
    public $next_plan; // Rencana selanjutnya (disimpan di notes sementara atau field baru, kita pakai notes mahasiswa)
    public $file; // File draft

    public $isModalOpen = false;

    public function mount()
    {
        $student = Auth::user()->student;
        
        // 1. Cek apakah sudah punya Skripsi yang DISETUJUI
        $this->thesis = Thesis::with(['supervisors.lecturer.user'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['APPROVED', 'ON_PROGRESS', 'COMPLETED'])
            ->first();

        if (!$this->thesis) {
            // Jika belum punya judul/belum di-acc, redirect ke pengajuan
            return redirect()->route('student.thesis.proposal')->with('error', 'Judul belum disetujui. Belum bisa isi log bimbingan.');
        }

        $this->refreshLogs();
        $this->guidance_date = date('Y-m-d');
    }

    public function refreshLogs()
    {
        $this->logs = ThesisLog::where('thesis_id', $this->thesis->id)
            ->orderBy('guidance_date', 'desc')
            ->get();
    }

    public function store()
    {
        $this->validate([
            'guidance_date' => 'required|date',
            'progress_report' => 'required|string|min:10',
            'file' => 'nullable|mimes:pdf,doc,docx|max:5120', // Max 5MB
        ]);

        $filePath = null;
        if ($this->file) {
            $filePath = $this->file->store('thesis-drafts', 'public');
        }

        ThesisLog::create([
            'thesis_id' => $this->thesis->id,
            'guidance_date' => $this->guidance_date,
            'student_notes' => $this->progress_report,
            'notes' => '', // Kosongkan, ini jatah dosen mengisi
            'file_attachment' => $filePath,
            'status' => 'DRAFT' // Default Draft, menunggu ACC Dosen
        ]);

        // Update status skripsi jadi ON_PROGRESS jika masih APPROVED
        if ($this->thesis->status == 'APPROVED') {
            $this->thesis->update(['status' => 'ON_PROGRESS']);
        }

        session()->flash('success', 'Catatan bimbingan berhasil disimpan.');
        $this->isModalOpen = false;
        $this->reset(['progress_report', 'file']);
        $this->refreshLogs();
    }

    public function delete($id)
    {
        $log = ThesisLog::where('id', $id)->where('thesis_id', $this->thesis->id)->first();
        
        if ($log && $log->status == 'DRAFT') {
            $log->delete();
            session()->flash('success', 'Catatan dihapus.');
            $this->refreshLogs();
        } else {
            session()->flash('error', 'Tidak bisa menghapus catatan yang sudah divalidasi Dosen.');
        }
    }

    public function render()
    {
        return view('livewire.student.thesis.thesis-log-index')->layout('layouts.student');
    }
}