<?php

namespace App\Livewire\Admin\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Billing;
use App\Models\Student;
use App\Models\AcademicPeriod;
use App\Models\StudyProgram;

class BillingIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_status = '';
    public $active_period;

    // Modal Create State
    public $isModalOpen = false;
    
    // Modal Detail State (BARU)
    public $isDetailModalOpen = false;
    public $selectedBillingDetail;

    // Tambahan Data Cicilan
    public $total_paid = 0;
    public $remaining_balance = 0;

    // Form Create Massal
    public $target_type = 'prodi'; 
    public $prodi_id, $entry_year, $specific_student_nim;
    public $title, $amount, $due_date;

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
        $this->due_date = date('Y-m-d', strtotime('+1 month')); 
    }

    public function create()
    {
        $this->reset(['prodi_id', 'entry_year', 'specific_student_nim', 'title', 'amount']);
        $this->isModalOpen = true;
    }

    // --- FITUR BARU: LIHAT DETAIL & HITUNG CICILAN ---
    public function showDetail($id)
    {
        // Eager load payments agar bisa lihat riwayat
        $this->selectedBillingDetail = Billing::with(['student.user', 'payments'])->find($id);
        
        // Hitung Total yang sudah dibayar (Hanya yang status VERIFIED)
        $this->total_paid = $this->selectedBillingDetail->payments
            ->where('status', 'VERIFIED')
            ->sum('amount_paid');

        // Hitung Sisa Tagihan
        $this->remaining_balance = $this->selectedBillingDetail->amount - $this->total_paid;

        $this->isDetailModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'title' => 'required',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        $students = collect();

        if ($this->target_type == 'prodi' && $this->prodi_id) {
            $students = Student::where('study_program_id', $this->prodi_id)->where('status', 'A')->get();
        } elseif ($this->target_type == 'angkatan' && $this->entry_year) {
            $students = Student::where('entry_year', $this->entry_year)->where('status', 'A')->get();
        } elseif ($this->target_type == 'individual' && $this->specific_student_nim) {
            $students = Student::where('nim', $this->specific_student_nim)->get();
        }

        if ($students->isEmpty()) {
            session()->flash('error', 'Tidak ada mahasiswa yang ditemukan dengan kriteria tersebut.');
            return;
        }

        $count = 0;
        foreach ($students as $student) {
            Billing::create([
                'student_id' => $student->id,
                'academic_period_id' => $this->active_period->id ?? null,
                'title' => $this->title,
                'description' => 'Dibuat oleh Admin',
                'amount' => $this->amount,
                'due_date' => $this->due_date,
                'status' => 'UNPAID'
            ]);
            $count++;
        }

        session()->flash('message', "Berhasil membuat tagihan untuk $count mahasiswa.");
        $this->isModalOpen = false;
    }

    public function render()
    {
        $billings = Billing::with(['student.user'])
            ->whereHas('student.user', fn($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.finance.billing-index', [
            'billings' => $billings,
            'prodis' => StudyProgram::all()
        ])->layout('layouts.admin');
    }
}