<?php

namespace App\Livewire\Pmb;

use Livewire\Component;
use App\Models\PmbWave;
use App\Models\Announcement;

class Info extends Component
{
    public function render()
    {
        // 1. Ambil Gelombang (Jadwal)
        $waves = PmbWave::where('is_active', true)
            ->orderBy('start_date')
            ->get();

        // 2. Ambil Pengumuman (Target: All atau Public/Camaba)
        // Asumsi target_role 'all' mencakup publik
        $announcements = Announcement::where('is_active', true)
            ->whereIn('target_role', ['all', 'camaba']) 
            ->latest()
            ->get();

        return view('livewire.pmb.info', [
            'waves' => $waves,
            'announcements' => $announcements
        ])->layout('layouts.pmb-landing');
    }
}