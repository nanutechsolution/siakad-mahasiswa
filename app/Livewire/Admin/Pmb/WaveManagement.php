<?php

namespace App\Livewire\Admin\Pmb;

use Livewire\Component;
use App\Models\PmbWave;

class WaveManagement extends Component
{
    public $waves;
    
    // Form Fields
    public $wave_id, $name, $start_date, $end_date, $is_active = true;
    public $isModalOpen = false;
    public $isEditMode = false;

    public function render()
    {
        $this->waves = PmbWave::orderBy('start_date', 'desc')->get();
        return view('livewire.admin.pmb.wave-management')->layout('layouts.admin');
    }

    public function create()
    {
        $this->reset(['wave_id', 'name', 'start_date', 'end_date', 'is_active']);
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $wave = PmbWave::find($id);
        $this->wave_id = $id;
        $this->name = $wave->name;
        $this->start_date = $wave->start_date->format('Y-m-d');
        $this->end_date = $wave->end_date->format('Y-m-d');
        $this->is_active = $wave->is_active;
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        PmbWave::updateOrCreate(['id' => $this->wave_id], [
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Data Gelombang berhasil disimpan.');
        $this->isModalOpen = false;
    }

    public function delete($id)
    {
        PmbWave::find($id)->delete();
        session()->flash('message', 'Gelombang dihapus.');
    }
    
    public function toggleActive($id)
    {
        $wave = PmbWave::find($id);
        $wave->update(['is_active' => !$wave->is_active]);
    }
}