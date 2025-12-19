<?php

namespace App\Livewire\Pmb;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Registrant;
use App\Enums\RegistrantStatus;
use App\Models\Billing;

class StatusPage extends Component
{
    public $registrant;
    public $billing;

    public function mount()
    {
        $this->registrant = Registrant::with('firstChoice')
            ->where('user_id', Auth::id())
            ->first();

        if (!$this->registrant) {
            return redirect()->route('pmb.register');
        }

        // Ambil billing aktif (SPP / daftar ulang)
        $this->billing = Billing::where('registrant_id', $this->registrant->id)
            ->whereIn('status', ['UNPAID', 'CICIL'])
            ->first();
    }

    public function render()
    {
        return view('livewire.pmb.status-page')->layout('layouts.pmb');
    }
}
