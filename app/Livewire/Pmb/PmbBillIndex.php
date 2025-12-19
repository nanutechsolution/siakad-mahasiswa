<?php

namespace App\Livewire\Pmb;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\Registrant;
use App\Models\Billing;
use App\Models\Payment;
use App\Models\Setting;

class PmbBillIndex extends Component
{
    use WithFileUploads;

    public $registrant;
    public $billing;
    
    // Form Upload
    public $isModalOpen = false;
    public $amount_paid;
    public $payment_date;
    public $proof_file;

    public function mount()
    {
        $this->registrant = Registrant::where('user_id', Auth::id())->first();
        
        if (!$this->registrant) {
            return redirect()->route('pmb.register');
        }

        // Ambil tagihan daftar ulang untuk camaba ini
        $this->billing = Billing::with('payments')
            ->where('registrant_id', $this->registrant->id)
            ->first();

        $this->payment_date = date('Y-m-d');
        if ($this->billing) {
            $this->amount_paid = $this->billing->amount; // Default bayar penuh
        }
    }

    public function storePayment()
    {
        $this->validate([
            'amount_paid' => 'required|numeric|min:100000', // Minimal cicil 100rb misal
            'payment_date' => 'required|date',
            'proof_file' => 'required|image|max:2048',
        ]);

        $path = $this->proof_file->store('payment-proofs', 'public');

        Payment::create([
            'billing_id' => $this->billing->id,
            'amount_paid' => $this->amount_paid,
            'payment_method' => 'TRANSFER',
            'proof_path' => $path,
            'payment_date' => $this->payment_date,
            'status' => 'PENDING',
        ]);

        session()->flash('success', 'Bukti pembayaran berhasil dikirim! Mohon tunggu verifikasi Admin Keuangan.');
        $this->isModalOpen = false;
        $this->reset(['proof_file']);
        
        // Refresh data
        $this->billing = Billing::with('payments')->find($this->billing->id);
    }

    public function render()
    {
        return view('livewire.pmb.pmb-bill-index', [
            'settings' => Setting::first()
        ])->layout('layouts.pmb');
    }
}