<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Models\AcademicPeriod;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    // Identitas & Pejabat
    public $campus_name, $campus_email, $campus_phone, $campus_address, $website_url;
    public $logo, $old_logo;
    public $foundation_name, $foundation_head, $rector_name, $rector_nip;
    
    // Data Bank (BARU)
    public $bank_name, $bank_account, $bank_holder;

    // Akademik
    public $active_period_id;
    public $allow_krs = false;
    public $allow_input_score = false;

    public function mount()
    {
        $setting = Setting::first();
        if ($setting) {
            $this->campus_name = $setting->campus_name;
            $this->campus_email = $setting->campus_email;
            $this->campus_phone = $setting->campus_phone;
            $this->campus_address = $setting->campus_address;
            $this->website_url = $setting->website_url;
            $this->old_logo = $setting->logo_path;
            
            $this->foundation_name = $setting->foundation_name;
            $this->foundation_head = $setting->foundation_head;
            $this->rector_name = $setting->rector_name;
            $this->rector_nip = $setting->rector_nip;

            // Load Data Bank
            $this->bank_name = $setting->bank_name;
            $this->bank_account = $setting->bank_account;
            $this->bank_holder = $setting->bank_holder;
        }

        $activePeriod = AcademicPeriod::where('is_active', true)->first();
        if ($activePeriod) {
            $this->active_period_id = $activePeriod->id;
            $this->allow_krs = (bool) $activePeriod->allow_krs;
            $this->allow_input_score = (bool) $activePeriod->allow_input_score;
        }
    }

    public function saveIdentity()
    {
        $this->validate([
            'campus_name' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'bank_account' => 'nullable|numeric', // Validasi sederhana
        ]);

        $setting = Setting::first();
        
        $data = [
            'campus_name' => $this->campus_name,
            'campus_email' => $this->campus_email,
            'campus_phone' => $this->campus_phone,
            'campus_address' => $this->campus_address,
            'website_url' => $this->website_url,
            
            'foundation_name' => $this->foundation_name,
            'foundation_head' => $this->foundation_head,
            'rector_name' => $this->rector_name,
            'rector_nip' => $this->rector_nip,

            // Simpan Data Bank
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'bank_holder' => $this->bank_holder,
        ];

        if ($this->logo) {
            $path = $this->logo->store('logos', 'public');
            $data['logo_path'] = $path;
        }

        $setting->update($data);
        session()->flash('message_identity', 'Identitas & Data Bank berhasil disimpan!');
    }

    public function saveAcademic()
    {
        AcademicPeriod::query()->update(['is_active' => false]);

        $period = AcademicPeriod::find($this->active_period_id);
        if ($period) {
            $period->update([
                'is_active' => true,
                'allow_krs' => $this->allow_krs,
                'allow_input_score' => $this->allow_input_score,
            ]);
        }

        session()->flash('message_academic', 'Pengaturan Akademik berhasil diperbarui!');
        return redirect()->route('admin.settings');
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'periods' => AcademicPeriod::orderBy('code', 'desc')->get()
        ])->layout('layouts.admin');
    }
}