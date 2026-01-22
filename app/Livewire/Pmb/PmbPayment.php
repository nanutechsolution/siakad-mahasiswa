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

    // Data Form
    public $amount_paid; // String agar bisa menampung format titik
    public $payment_date;
    public $proof_file;

    // Data UI
    public $tahap_label; // Label tahapan (50%, 25%, dll)
    public $banks = [];  // Array untuk menampung banyak bank

    public function mount()
    {
        $this->registrant = Registrant::where('user_id', Auth::id())->first();

        // Cek status dasar
        if (!$this->registrant || $this->registrant->status->value !== 'ACCEPTED') {
            return redirect()->route('pmb.status');
        }

        $this->billing = Billing::where('registrant_id', $this->registrant->id)->first();
        
        if (!$this->billing) {
            abort(404, 'Tagihan belum diterbitkan.');
        }

        $this->payment_date = date('Y-m-d');

        // --- 1. SETUP MULTI BANK (Bisa diganti ambil dari DB jika ada tabel bank) ---
        // Jika Anda punya tabel Bank, ganti ini dengan: Bank::all();
        $this->banks = [
            [
                'bank' => 'BANK BRI',
                'no_rek' => '1234-5678-9000',
                'an' => 'YAYASAN UNIVERSITAS A',
                'logo' => 'text-blue-600' // class warna opsional
            ],
            [
                'bank' => 'BANK BNI',
                'no_rek' => '9876-5432-1000',
                'an' => 'YAYASAN UNIVERSITAS A',
                'logo' => 'text-orange-600'
            ],
            [
                'bank' => 'BANK MANDIRI',
                'no_rek' => '111-00-222-333',
                'an' => 'YAYASAN UNIVERSITAS A',
                'logo' => 'text-indigo-600'
            ],
        ];

        // --- 2. LOGIKA CICILAN 50% - 25% - 25% ---
        $this->calculateInstallment();
    }

    public function calculateInstallment()
    {
        $totalTagihan = $this->billing->amount;
        // Hanya hitung pembayaran yang sudah diverifikasi admin
        $sudahBayar = $this->billing->payments()->where('status', 'VERIFIED')->sum('amount_paid');
        
        $targetTahap1 = $totalTagihan * 0.50; // 50%
        $targetTahap2 = $totalTagihan * 0.75; // 50% + 25% = 75%
        
        $kurangBayar = 0;

        // Cek User ada di tahap mana
        if ($sudahBayar < $targetTahap1) {
            // TAHAP 1: User belum lunas 50%
            $kurangBayar = $targetTahap1 - $sudahBayar;
            $this->tahap_label = "Tahap 1 (Wajib 50%)";
        } 
        elseif ($sudahBayar < $targetTahap2) {
            // TAHAP 2: User sudah lunas 50%, kejar target 75%
            $kurangBayar = $targetTahap2 - $sudahBayar;
            $this->tahap_label = "Tahap 2 (Cicilan 25%)";
        } 
        else {
            // TAHAP 3: Pelunasan sisa (100%)
            $kurangBayar = $totalTagihan - $sudahBayar;
            $this->tahap_label = "Tahap 3 (Pelunasan Akhir)";
        }

        // Jika sudah lunas total
        if ($kurangBayar <= 0) {
            $kurangBayar = 0;
            $this->tahap_label = "Lunas";
        }

        // Format Rupiah dengan titik untuk tampilan awal input
        $this->amount_paid = number_format($kurangBayar, 0, ',', '.');
    }

    public function submitPayment()
    {
        // 1. Bersihkan format titik dari input (Contoh: "5.000.000" jadi "5000000")
        $cleanAmount = str_replace('.', '', $this->amount_paid);

        $this->validate([
            'amount_paid' => 'required', // Validasi string dulu
            'payment_date' => 'required|date',
            'proof_file' => 'required|image|max:2048',
        ]);

        // Validasi numeric manual setelah dibersihkan
        if (!is_numeric($cleanAmount) || $cleanAmount < 10000) {
            $this->addError('amount_paid', 'Nominal tidak valid.');
            return;
        }

        // Simpan File
        $path = $this->proof_file->store('pmb/payments', 'public');

        // Buat Record
        Payment::create([
            'billing_id' => $this->billing->id,
            'amount_paid' => $cleanAmount, // Simpan angka bersih
            'payment_method' => 'TRANSFER',
            'proof_path' => $path,
            'payment_date' => $this->payment_date,
            'status' => 'PENDING',
        ]);

        session()->flash('success', 'Bukti berhasil dikirim. Tunggu verifikasi admin.');
        
        // Reset form & hitung ulang (tapi karena redirect biasanya reset sendiri)
        return redirect()->route('pmb.status');
    }

    public function render()
    {
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