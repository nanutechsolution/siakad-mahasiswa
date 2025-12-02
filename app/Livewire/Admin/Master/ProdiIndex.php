<?php

namespace App\Livewire\Admin\Master;

use App\Models\StudyProgram;
use App\Models\StructureHistory; 
use Livewire\Component;
use Livewire\WithPagination;

class ProdiIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;
    public $isHistoryOpen = false;
    public $historyList = [];

    // Form Fields
    public $prodi_id;
    public $faculty_id;
    public $code, $name, $degree = 'S1';
    public $total_credits = 144; // <--- TAMBAHAN DEFAULT
    public $head_name, $head_nip;
    public $head_start_date; 

    public function render()
    {
        $prodis = StudyProgram::with('faculty')
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->orderBy('code')
            ->paginate(10);

        return view('livewire.admin.master.prodi-index', [
            'prodis' => $prodis
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
        $prodi = StudyProgram::find($id);
        
        $this->prodi_id = $id;
        $this->faculty_id = $prodi->faculty_id;
        $this->code = $prodi->code;
        $this->name = $prodi->name;
        $this->degree = $prodi->degree;
        $this->total_credits = $prodi->total_credits; // <--- Load dari DB
        $this->head_name = $prodi->head_name;
        $this->head_nip = $prodi->head_nip;
        
        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'code' => 'required|unique:study_programs,code,' . $this->prodi_id,
            'name' => 'required',
            'degree' => 'required',
            'total_credits' => 'required|integer|min:10|max:200', // <--- Validasi
        ]);

        // ... (Logic history kaprodi tetap sama, saya singkat biar fokus) ...
        $oldProdi = $this->isEditMode ? StudyProgram::find($this->prodi_id) : null;

        $prodi = StudyProgram::updateOrCreate(['id' => $this->prodi_id], [
            'faculty_id' => $this->faculty_id,
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'degree' => $this->degree,
            'total_credits' => $this->total_credits, // <--- Simpan ke DB
            'head_name' => $this->head_name,
            'head_nip' => $this->head_nip,
        ]);

        // ... (Logic history kaprodi copy dari sebelumnya) ...
        if ((!$this->isEditMode && $this->head_name) || ($this->isEditMode && $oldProdi->head_name !== $this->head_name)) {
             if ($oldProdi) {
                StructureHistory::where('structurable_type', StudyProgram::class)
                    ->where('structurable_id', $prodi->id)->where('position', 'Kaprodi')
                    ->update(['is_active' => false, 'end_date' => now()]);
            }
            if ($this->head_name) {
                StructureHistory::create([
                    'structurable_type' => StudyProgram::class,
                    'structurable_id' => $prodi->id,
                    'position' => 'Kaprodi',
                    'official_name' => $this->head_name,
                    'official_nip' => $this->head_nip,
                    'start_date' => now(),
                    'is_active' => true,
                ]);
            }
        }

        session()->flash('message', 'Data Prodi berhasil disimpan.');
        $this->isModalOpen = false;
        $this->resetFields();
    }

    // ... (method delete & showHistory tetap sama) ...
    public function delete($id) { StudyProgram::find($id)->delete(); session()->flash('message', 'Hapus sukses'); }
    public function showHistory($id) { /* logic history */ $this->isHistoryOpen = true; }

    private function resetFields()
    {
        $this->reset([
            'prodi_id', 'faculty_id', 'code', 'name', 'degree', 
            'total_credits', 'head_name', 'head_nip'
        ]);
        $this->total_credits = 144; // Reset default
    }
}