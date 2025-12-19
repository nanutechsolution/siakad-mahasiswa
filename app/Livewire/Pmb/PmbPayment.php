<?php

namespace App\Livewire\Pmb;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\Registrant;
use App\Models\Billing;
use App\Models\Payment;
use App\Models\Setting;

class PmbPayment extends Component
{
    use WithFileUploads;

    public $registrant;
    public $billing;
    public $settings;

    // Form Fields
    public $amount_paid;
    public $payment_date;
    public $proof_file;

    public function mount()
    {
        $this->registrant = Registrant::where('user_id', Auth::id())->first();

        if (!$this->registrant || $this->registrant->status->value !== 'ACCEPTED') {
            return redirect()->route('pmb.status');
        }

        // Cari tagihan pendaftaran ulang
        $this->billing = Billing::where('registrant_id', $this->registrant->id)->first();
        
        if (!$this->billing) {
            abort(404, 'Tagihan belum diterbitkan. Hubungi Panitia PMB.');
        }

        $this->settings = Setting::first();
        $this->payment_date = date('Y-m-d');
        
        // Default nominal bayar adalah sisa tagihan
        $totalPaid = $this->billing->payments()->where('status', 'VERIFIED')->sum('amount_paid');
        $this->amount_paid = $this->billing->amount - $totalPaid;
    }

    public function submitPayment()
    {
        $this->validate([
            'amount_paid' => 'required|numeric|min:50000', // Minimal bayar 50rb
            'payment_date' => 'required|date',
            'proof_file' => 'required|image|max:2048', // Max 2MB
        ]);

        // Simpan File
        $path = $this->proof_file->store('pmb/payments', 'public');

        // Buat Record Pembayaran
        Payment::create([
            'billing_id' => $this->billing->id,
            'amount_paid' => $this->amount_paid,
            'payment_method' => 'TRANSFER',
            'proof_path' => $path,
            'payment_date' => $this->payment_date,
            'status' => 'PENDING',
        ]);

        session()->flash('success', 'Bukti pembayaran berhasil diunggah. Mohon tunggu verifikasi Admin untuk mendapatkan NIM.');
        return redirect()->route('pmb.status');
    }

    public function render()
    {
        // Hitung riwayat pembayaran
        $payments = $this->billing->payments()->latest()->get();
        $totalVerified = $payments->where('status', 'VERIFIED')->sum('amount_paid');
        $remaining = $this->billing->amount - $totalVerified;

        return view('livewire.pmb.pmb-payment', [
            'payments' => $payments,
            'total_verified' => $totalVerified,
            'remaining' => $remaining
        ])->layout('layouts.pmb');
    }
}