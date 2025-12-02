<?php

namespace App\Livewire\Student\Finance;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\Billing;
use App\Models\Payment;

class BillIndex extends Component
{
    use WithFileUploads;

    public $student;
    public $billings;

    // Modal Payment
    public $isModalOpen = false;
    public $selectedBilling;
    public $amount_paid;
    public $payment_date;
    public $proof_file;

    public function mount()
    {
        $this->student = Auth::user()->student;
        $this->payment_date = date('Y-m-d'); // Default hari ini
    }

    public function pay($billingId)
    {
        $this->selectedBilling = Billing::find($billingId);
        $this->amount_paid = $this->selectedBilling->amount; // Default bayar penuh
        $this->isModalOpen = true;
    }

    public function storePayment()
    {
        $this->validate([
            'amount_paid' => 'required|numeric|min:10000',
            'payment_date' => 'required|date',
            'proof_file' => 'required|image|max:2048', // Max 2MB
        ]);

        // Simpan Bukti
        $path = $this->proof_file->store('payment-proofs', 'public');

        // Buat Record Pembayaran
        Payment::create([
            'billing_id' => $this->selectedBilling->id,
            'amount_paid' => $this->amount_paid,
            'payment_method' => 'TRANSFER',
            'proof_path' => $path,
            'payment_date' => $this->payment_date,
            'status' => 'PENDING', // Menunggu verifikasi admin
        ]);

        session()->flash('success', 'Bukti pembayaran berhasil dikirim! Tunggu verifikasi Admin.');
        $this->isModalOpen = false;
        $this->reset(['proof_file', 'selectedBilling']);
    }

    public function render()
    {
        $this->billings = Billing::with(['payments' => function($q) {
            $q->latest(); // Ambil pembayaran terbaru untuk status
        }])
        ->where('student_id', $this->student->id)
        ->latest()
        ->get();

        return view('livewire.student.finance.bill-index')->layout('layouts.student');
    }
}