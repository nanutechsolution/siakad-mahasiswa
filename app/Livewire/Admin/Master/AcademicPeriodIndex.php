<?php

namespace App\Livewire\Admin\Master;

use App\Models\AcademicPeriod;
use Livewire\Component;
use Livewire\WithPagination;

class AcademicPeriodIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;

    // Form Fields
    public $period_id;
    public $code;       // Contoh: 20241
    public $name;       // Contoh: Ganjil 2024/2025
    public $start_date;
    public $end_date;

    public function render()
    {
        $periods = AcademicPeriod::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('code', 'like', '%'.$this->search.'%')
            ->orderBy('code', 'desc') // Yang terbaru paling atas
            ->paginate(10);

        return view('livewire.admin.master.academic-period-index', [
            'periods' => $periods
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->resetFields();
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $period = AcademicPeriod::find($id);
        $this->period_id = $id;
        $this->code = $period->code;
        $this->name = $period->name;
        // Format tanggal agar bisa masuk ke input type="date"
        $this->start_date = $period->start_date ? $period->start_date->format('Y-m-d') : null;
        $this->end_date = $period->end_date ? $period->end_date->format('Y-m-d') : null;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|numeric|unique:academic_periods,code,' . $this->period_id,
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        AcademicPeriod::updateOrCreate(['id' => $this->period_id], [
            'code' => $this->code,
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            // Defaultnya tidak aktif & krs tutup (Diaktifkan lewat menu Settings)
            // Kecuali jika create baru, kita set default false.
        ]);

        session()->flash('message', 'Data Periode berhasil disimpan.');
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function delete($id)
    {
        $period = AcademicPeriod::find($id);
        
        // Jangan hapus semester yang sedang aktif!
        if($period->is_active) {
            session()->flash('error', 'Gagal! Periode ini sedang AKTIF. Ganti periode aktif dulu di menu Pengaturan.');
            return;
        }

        $period->delete();
        session()->flash('message', 'Periode dihapus.');
    }

    private function resetFields()
    {
        $this->period_id = null;
        $this->code = '';
        $this->name = '';
        $this->start_date = '';
        $this->end_date = '';
    }
}