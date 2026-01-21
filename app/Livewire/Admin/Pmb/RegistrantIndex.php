<?php

namespace App\Livewire\Admin\Pmb;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Registrant;
use App\Models\Student;
use App\Models\Billing;
use App\Models\StudyProgram;
use App\Enums\RegistrantStatus;
use Illuminate\Support\Facades\DB;
use App\Services\NimGeneratorService;

class RegistrantIndex extends Component
{
    use WithPagination;

    // Filter & Search
    public $search = '';
    public $filter_prodi = '';
    
    // Filter Pintar (Stage)
    public $filter_stage = '';

    // Modal & Data Selection
    public $isModalOpen = false;
    public $selectedRegistrant;
    public $selectedBilling;

    // Statistik Keuangan
    public $total_paid = 0;
    public $remaining_balance = 0;
    public $payment_progress = 0;

    public function render()
    {
        $registrants = Registrant::with(['user', 'firstChoice', 'billing.payments']) 
            ->where('status', RegistrantStatus::ACCEPTED)
            
            ->when($this->search, function ($q) {
                $q->where('registration_no', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'));
            })
            ->when($this->filter_prodi, fn($q) => $q->where('first_choice_id', $this->filter_prodi))
            ->when($this->filter_stage, function($q) {
                if ($this->filter_stage == 'active') {
                    $q->whereHas('user', fn($u) => $u->where('role', 'student'));
                } 
                elseif ($this->filter_stage == 'ready') {
                    $q->whereHas('billing', fn($b) => $b->whereIn('status', ['PAID', 'PARTIAL']))
                      ->whereHas('user', fn($u) => $u->where('role', '!=', 'student'));
                } 
                elseif ($this->filter_stage == 'unpaid') {
                    $q->where(function($sub) {
                        $sub->whereDoesntHave('billing')
                            ->orWhereHas('billing', fn($b) => $b->whereNotIn('status', ['PAID', 'PARTIAL']));
                    })->whereHas('user', fn($u) => $u->where('role', '!=', 'student'));
                }
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.pmb.registrant-index', [
            'registrants' => $registrants,
            'prodis' => StudyProgram::all(),
        ])->layout('layouts.admin');
    }

    /**
     * Tampilkan Detail Camaba & Cari Tagihan secara cerdas
     */
    public function showDetail($id)
    {
        $this->selectedRegistrant = Registrant::with(['user', 'firstChoice'])->find($id);

        $billing = Billing::with('payments')
            ->where('registrant_id', $id)
            ->orWhere(function ($query) use ($id) {
                $reg = Registrant::find($id);
                $query->whereHas('student', function ($q) use ($reg) {
                    $q->where('user_id', $reg->user_id);
                });
            })
            ->first();

        $this->selectedBilling = $billing;

        if ($billing) {
            $this->total_paid = $billing->payments->where('status', 'VERIFIED')->sum('amount_paid');
            $this->remaining_balance = max(0, $billing->amount - $this->total_paid);
            $this->payment_progress = $billing->amount > 0 ? round(($this->total_paid / $billing->amount) * 100) : 0;
        } else {
            $this->total_paid = 0;
            $this->remaining_balance = 0;
            $this->payment_progress = 0;
        }

        $this->isModalOpen = true;
    }

    public function promoteToStudent($registrantId)
    {
        $camaba = Registrant::find($registrantId);
        
        $existingStudent = Student::where('user_id', $camaba->user_id)->first();
        if ($existingStudent) {
            session()->flash('error', 'Mahasiswa ini sudah aktif dengan NIM: ' . $existingStudent->nim);
            return;
        }

        $billing = Billing::where('registrant_id', $registrantId)->first();

        if (!$billing || !in_array($billing->status, ['PAID', 'PARTIAL'])) {
            session()->flash('error', 'Gagal! Camaba ini belum melakukan pembayaran yang valid.');
            return;
        }

        DB::transaction(function () use ($camaba, $billing) {
            $nimService = new NimGeneratorService();
            $newNim = $nimService->generate($camaba->first_choice_id, date('Y'));

            $student = Student::create([
                'user_id' => $camaba->user_id,
                'study_program_id' => $camaba->first_choice_id,
                'nim' => $newNim,
                'entry_year' => date('Y'),
                'status' => 'A',
            ]);

            $camaba->user->update([
                'role' => 'student',
                'username' => $newNim
            ]);

            $billing->update([
                'student_id' => $student->id,
                'registrant_id' => null
            ]);
        });

        session()->flash('message', 'Aktivasi Berhasil! Mahasiswa resmi terdaftar.');
        $this->showDetail($registrantId);
    }

    public function reject()
    {
        $this->selectedRegistrant->update(['status' => RegistrantStatus::REJECTED]);
        session()->flash('message', 'Kelulusan calon mahasiswa dibatalkan.');
        $this->isModalOpen = false;
    }
}