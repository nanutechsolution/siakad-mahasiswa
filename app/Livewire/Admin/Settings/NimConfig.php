<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Models\Setting;
use App\Services\NimGeneratorService;

class NimConfig extends Component
{
    public $year_format = 'YY';
    public $prodi_source = 'CODE';
    public $seq_digit = 4;
    
    public $preview_nim = '24TI0001'; // Default fallback

    public function mount()
    {
        $setting = Setting::first();
        $config = $setting->nim_config;

        if ($config) {
            $this->year_format = $config['year_format'] ?? 'YY';
            $this->prodi_source = $config['prodi_source'] ?? 'CODE';
            $this->seq_digit = $config['seq_digit'] ?? 4;
        }
        
        $this->generatePreview();
    }

    public function updated()
    {
        $this->generatePreview();
    }

    public function generatePreview()
    {
        // Ensure Service exists, if not create a simple local logic to avoid error
        if (class_exists(NimGeneratorService::class)) {
            $service = new NimGeneratorService();
            $this->preview_nim = $service->preview([
                'year_format' => $this->year_format,
                'prodi_source' => $this->prodi_source,
                'seq_digit' => $this->seq_digit,
            ]);
        } else {
            // Fallback logic if service missing
            $year = date('Y');
            $y = ($this->year_format == 'YYYY') ? $year : substr($year, -2);
            $p = ($this->prodi_source == 'CODE') ? 'TI' : '01';
            $s = str_pad('1', $this->seq_digit, '0', STR_PAD_LEFT);
            $this->preview_nim = $y . $p . $s;
        }
    }

    public function save()
    {
        $setting = Setting::first();
        
        $config = [
            'year_format' => $this->year_format,
            'prodi_source' => $this->prodi_source,
            'seq_digit' => $this->seq_digit,
        ];

        if ($setting) {
            $setting->update(['nim_config' => $config]);
            session()->flash('message', 'Format NIM berhasil disimpan.');
        }
    }

    public function render()
    {
        return view('livewire.admin.settings.nim-config')->layout('layouts.admin');
    }
}