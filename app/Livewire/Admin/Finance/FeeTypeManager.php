<?php

namespace App\Livewire\Admin\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\FeeType;

class FeeTypeManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;
    
    // Form
    public $type_id, $code, $name, $is_active = true;

    public function render()
    {
        $types = FeeType::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('code', 'like', '%'.$this->search.'%')
            ->orderBy('code')
            ->paginate(10);

        return view('livewire.admin.finance.fee-type-manager', [
            'types' => $types
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->reset(['type_id', 'code', 'name', 'is_active']);
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $type = FeeType::find($id);
        $this->type_id = $id;
        $this->code = $type->code;
        $this->name = $type->name;
        $this->is_active = $type->is_active;
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'code' => 'required|unique:fee_types,code,' . $this->type_id,
            'name' => 'required',
        ]);

        FeeType::updateOrCreate(['id' => $this->type_id], [
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'is_active' => $this->is_active,
        ]);

        session()->flash('message', 'Jenis Biaya berhasil disimpan.');
        $this->isModalOpen = false;
    }

    public function delete($id)
    {
        // Cek apakah sudah dipakai di Billing? (Sebaiknya dicek dulu)
        // if(Billing::where('category', FeeType::find($id)->code)->exists()) { ... error ... }
        
        FeeType::find($id)->delete();
        session()->flash('message', 'Dihapus.');
    }
}