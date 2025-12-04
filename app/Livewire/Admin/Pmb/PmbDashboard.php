<?php

namespace App\Livewire\Admin\Pmb;

use Livewire\Component;
use App\Models\Registrant;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PmbDashboard extends Component
{
    public function render()
    {
        // 1. Statistik Card Utama
        $total_pendaftar = Registrant::count();
        $total_submit = Registrant::where('status', '!=', 'DRAFT')->count();
        $total_diterima = Registrant::where('status', 'ACCEPTED')->count();
        
        // Hitung Konversi (Berapa % yang daftar ulang/jadi mahasiswa)
        $total_daftar_ulang = \App\Models\Student::whereYear('created_at', date('Y'))->count();

        // 2. Statistik Per Prodi (Pilihan 1)
        $stats_prodi = Registrant::select('first_choice_id', DB::raw('count(*) as total'))
            ->with('firstChoice')
            ->groupBy('first_choice_id')
            ->orderByDesc('total')
            ->get();

        // 3. Statistik Harian (7 Hari Terakhir) - Untuk Grafik
        $chart_data = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Registrant::whereDate('created_at', $date)->count();
            $chart_data->push([
                'date' => $date->format('d M'),
                'count' => $count
            ]);
        }

        return view('livewire.admin.pmb.pmb-dashboard', [
            'total_pendaftar' => $total_pendaftar,
            'total_submit' => $total_submit,
            'total_diterima' => $total_diterima,
            'total_daftar_ulang' => $total_daftar_ulang,
            'stats_prodi' => $stats_prodi,
            'chart_labels' => $chart_data->pluck('date'),
            'chart_values' => $chart_data->pluck('count'),
        ])->layout('layouts.admin');
    }
}