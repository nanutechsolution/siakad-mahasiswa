<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;
use App\Models\StudyProgram;
use App\Services\NimGeneratorService;

class NimConfig extends Component
{
    public $year_format = 'YY';
    public $seq_digit = 4;
    
    // Array untuk menyimpan kode custom per prodi
    // Format: [ prodi_id => '55', prodi_id => '57' ]
    public $prodi_codes = []; 

    public $simulations = [];
    public $all_prodis; // Cache data prodi

    public function mount()
    {
        $setting = Setting::first();
        $config = $setting->nim_config;

        // Load Prodi dulu
        $this->all_prodis = StudyProgram::orderBy('name')->get();

        // Load Config
        if ($config) {
            $this->year_format = $config['year_format'] ?? 'YY';
            $this->seq_digit = $config['seq_digit'] ?? 4;
            
            // Load kode yang sudah tersimpan, atau default kosong
            $savedCodes = $config['prodi_codes'] ?? [];
            
            // Inisialisasi array prodi_codes agar wire:model tidak error
            foreach($this->all_prodis as $p) {
                // Jika sudah ada settingan pakai itu, jika belum pakai kode default (TI/SI)
                $this->prodi_codes[$p->id] = $savedCodes[$p->id] ?? $p->code;
            }
        } else {
             // Default awal
             foreach($this->all_prodis as $p) {
                $this->prodi_codes[$p->id] = $p->code;
            }
        }
        
        $this->generateSimulations();
    }

    // Setiap ada perubahan input, update preview
    public function updated()
    {
        $this->generateSimulations();
    }

    public function generateSimulations()
    {
        $service = new NimGeneratorService();
        
        // Config temporary untuk preview
        $config = [
            'year_format' => $this->year_format,
            'prodi_codes' => $this->prodi_codes, // Kirim array kode custom
            'seq_digit' => $this->seq_digit,
        ];

        $this->simulations = $this->all_prodis->map(function($prodi) use ($service, $config) {
            return [
                'name' => $prodi->name,
                'code_default' => $prodi->code,
                'code_used' => $this->prodi_codes[$prodi->id] ?? '-',
                'example' => $service->preview($config, $prodi)
            ];
        })->toArray();
    }

    public function save()
    {
        $setting = Setting::first();
        
        $config = [
            'year_format' => $this->year_format,
            'prodi_codes' => $this->prodi_codes, // Simpan array mapping
            'seq_digit' => $this->seq_digit,
        ];

        if ($setting) {
            $setting->update(['nim_config' => $config]);
            session()->flash('message', 'Konfigurasi Kode Prodi berhasil disimpan.');
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.nim-config')->layout('layouts.admin');
    }
}