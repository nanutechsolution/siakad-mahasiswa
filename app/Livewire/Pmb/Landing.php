<?php

namespace App\Livewire\Pmb;

use Livewire\Component;
use App\Models\PmbWave;
use App\Models\StudyProgram;
use Carbon\Carbon;

class Landing extends Component
{
    public function render()
    {
        // Ambil Gelombang Aktif
        $active_wave = PmbWave::where('is_active', true)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->first();

        // Ambil list prodi untuk dipamerkan
        $prodis = StudyProgram::with('faculty')->orderBy('name')->get();

        return view('livewire.pmb.landing', [
            'active_wave' => $active_wave,
            'prodis' => $prodis
        ])->layout('layouts.pmb-landing'); // Gunakan layout guest yang bersih
    }
}
