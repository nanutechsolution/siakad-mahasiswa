<?php

namespace App\Admin\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payment;
use App\Models\Billing;
use Illuminate\Support\Facades\DB;

class PaymentVerification extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_status = 'PENDING'; // Default tampilkan yang nunggu verifikasi
    
    // Modal State
    public $isModalOpen = false;
    public $selectedPayment;
    public $rejection_note = '';

    public function render()
    {
        $payments = Payment::with(['billing.registrant.user', 'billing.student.user'])
            ->when($this->search, function($q) {
                $q->whereHas('billing.registrant.user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'))
                  ->orWhereHas('billing.student.user', fn($u) => $u->where('name', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filter_status, fn($q) => $q->where('status', $this->filter_status))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.finance.payment-verification', [
            'payments' => $payments
        ])->layout('layouts.admin');
    }

    public function showDetail($id)
    {
        $this->selectedPayment = Payment::with('billing')->find($id);
        $this->rejection_note = '';
        $this->isModalOpen = true;
    }

    /**
     * TERIMA PEMBAYARAN
     */
    public function approve()
    {
        DB::transaction(function() {
            $payment = $this->selectedPayment;
            $payment->update(['status' => 'VERIFIED']);

            // Update Status Tagihan (Billing)
            $billing = $payment->billing;
            $totalVerified = $billing->payments()->where('status', 'VERIFIED')->sum('amount_paid');

            if ($totalVerified >= $billing->amount) {
                $billing->update(['status' => 'PAID']);
            } elseif ($totalVerified > 0) {
                $billing->update(['status' => 'PARTIAL']);
            }
        });

        session()->flash('message', 'Pembayaran berhasil diverifikasi.');
        $this->isModalOpen = false;
    }

    /**
     * TOLAK PEMBAYARAN
     */
    public function reject()
    {
        $this->validate([
            'rejection_note' => 'required|min:5'
        ]);

        $this->selectedPayment->update([
            'status' => 'REJECTED',
            'rejection_note' => $this->rejection_note
        ]);

        session()->flash('error', 'Pembayaran ditolak.');
        $this->isModalOpen = false;
    }
}