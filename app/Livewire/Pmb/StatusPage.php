<?php

namespace App\Livewire\Pmb;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Registrant;
use App\Enums\RegistrantStatus;

class StatusPage extends Component
{
    public $registrant;

    public function mount()
    {
        // Ambil data pendaftaran user yang sedang login
        $this->registrant = Registrant::with(['firstChoice', 'secondChoice'])
            ->where('user_id', Auth::id())
            ->first();

        // Jika belum daftar sama sekali, lempar ke formulir
        if (!$this->registrant) {
            return redirect()->route('pmb.register');
        }
        
        // Jika status masih DRAFT, lempar kembali ke wizard untuk diselesaikan
        if ($this->registrant->status === RegistrantStatus::DRAFT) {
            return redirect()->route('pmb.register');
        }
    }

    public function render()
    {
        return view('livewire.pmb.status-page')->layout('layouts.pmb');
    }
}