<?php

namespace App\Livewire\Admin\Finance;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TuitionRate;
use App\Models\StudyProgram;
use App\Models\FeeType; 

class TuitionRateManager extends Component
{
    use WithPagination;

    public $search = '';
    public $filter_prodi = '';

    // Modal State
    public $isModalOpen = false;
    public $isEditMode = false;

    // Form Fields (Update fee_type jadi fee_type_id)
    public $rate_id, $study_program_id, $entry_year, $amount, $fee_type_id;

    public function mount()
    {
        $this->entry_year = date('Y'); 
    }

    public function render()
    {
        // Tambahkan with('fee_type') untuk eager loading
        $rates = TuitionRate::with(['study_program', 'fee_type'])
            ->when($this->search, function($q) {
                $q->where('entry_year', 'like', '%'.$this->search.'%')
                  ->orWhereHas('study_program', fn($p) => $p->where('name', 'like', '%'.$this->search.'%'));
            })
            ->when($this->filter_prodi, fn($q) => $q->where('study_program_id', $this->filter_prodi))
            ->orderBy('entry_year', 'desc')
            ->orderBy('study_program_id')
            ->paginate(10);

        return view('livewire.admin.finance.tuition-rate-manager', [
            'rates' => $rates,
            'prodis' => StudyProgram::all(),
            'fee_types' => FeeType::where('is_active', true)->get()
        ])->layout('layouts.admin');
    }

    public function create()
    {
        $this->reset(['rate_id', 'study_program_id', 'entry_year', 'amount', 'fee_type_id']);
        $this->entry_year = date('Y');
        // Default ke SPP jika ada, atau elemen pertama
        $defaultType = FeeType::where('code', 'SPP')->first();
        $this->fee_type_id = $defaultType ? $defaultType->id : null;
        
        $this->isEditMode = false;
        $this->isModalOpen = true;
    }

    public function edit($id)
    {
        $rate = TuitionRate::findOrFail($id);
        $this->rate_id = $id;
        $this->study_program_id = $rate->study_program_id;
        $this->entry_year = $rate->entry_year;
        $this->amount = $rate->amount;
        $this->fee_type_id = $rate->fee_type_id; // Load ID
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'study_program_id' => 'required|exists:study_programs,id',
            'entry_year' => 'required|digits:4',
            'amount' => 'required|numeric|min:0',
            'fee_type_id' => 'required|exists:fee_types,id', // Validasi Relasi
        ]);

        TuitionRate::updateOrCreate(
            [
                'study_program_id' => $this->study_program_id,
                'entry_year' => $this->entry_year,
                'fee_type_id' => $this->fee_type_id, // Gunakan ID sebagai kunci unik
            ],
            [
                'amount' => $this->amount,
                'is_active' => true
            ]
        );

        session()->flash('message', 'Tarif berhasil disimpan.');
        $this->isModalOpen = false;
    }

    public function delete($id)
    {
        TuitionRate::find($id)->delete();
        session()->flash('message', 'Tarif dihapus.');
    }
}