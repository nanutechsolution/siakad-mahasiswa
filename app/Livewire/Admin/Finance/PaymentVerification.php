<?php

namespace App\Livewire\Admin\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\Billing;
use Illuminate\Support\Facades\Auth;

class PaymentVerification extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_status = 'PENDING'; // Default tampilkan yang butuh verifikasi

    // Modal State
    public $isModalOpen = false;
    public $selectedPayment;
    public $rejection_note;

    public function render()
    {
        $payments = Payment::with(['billing.student.user'])
            ->where('status', $this->filter_status)
            ->whereHas('billing.student.user', function($q) {
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'asc') // Yang lama di atas (FIFO)
            ->paginate(10);

        return view('livewire.admin.finance.payment-verification', [
            'payments' => $payments
        ])->layout('layouts.admin');
    }

    public function showDetail($id)
    {
        $this->selectedPayment = Payment::with('billing.student')->find($id);
        $this->rejection_note = '';
        $this->isModalOpen = true;
    }

    public function verify()
    {
        if (!$this->selectedPayment) return;

        // 1. Update Status Pembayaran
        $this->selectedPayment->update([
            'status' => 'VERIFIED',
            'verified_by' => Auth::id()
        ]);

        // 2. Update Status Tagihan (Billing)
        // Hitung total yang sudah dibayar (verified)
        $billing = $this->selectedPayment->billing;
        $totalPaid = $billing->payments()->where('status', 'VERIFIED')->sum('amount_paid');

        if ($totalPaid >= $billing->amount) {
            $billing->update(['status' => 'PAID']);
        } else {
            $billing->update(['status' => 'PARTIAL']);
        }

        session()->flash('message', 'Pembayaran berhasil diverifikasi.');
        $this->isModalOpen = false;
    }

    public function reject()
    {
        $this->validate([
            'rejection_note' => 'required|string|min:5'
        ]);

        if (!$this->selectedPayment) return;

        $this->selectedPayment->update([
            'status' => 'REJECTED',
            'rejection_note' => $this->rejection_note,
            'verified_by' => Auth::id()
        ]);

        session()->flash('message', 'Pembayaran ditolak. Mahasiswa akan diberitahu.');
        $this->isModalOpen = false;
    }
}