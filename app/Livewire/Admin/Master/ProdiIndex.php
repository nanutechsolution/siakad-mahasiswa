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

    // Untuk Fitur Riwayat
    public $isHistoryOpen = false;
    public $historyList = [];

    // Form Fields
    public $prodi_id;
    public $faculty_id;
    public $code, $name, $degree = 'S1';
    public $total_credits = 144;
    public $head_name, $head_nip;
    public $head_start_date;
    
    // Config Baru
    public $is_package = false; // Default SKS (Non-Paket)

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
        $prodi = StudyProgram::findOrFail($id);
        
        $this->prodi_id = $id;
        $this->faculty_id = $prodi->faculty_id;
        $this->code = $prodi->code;
        $this->name = $prodi->name;
        $this->degree = $prodi->degree;
        $this->total_credits = $prodi->total_credits;
        $this->head_name = $prodi->head_name;
        $this->head_nip = $prodi->head_nip;
        $this->is_package = (bool) $prodi->is_package; // <--- Load status paket
        
        // Ambil Tanggal Mulai Menjabat
        $activeHistory = StructureHistory::where('structurable_type', StudyProgram::class)
            ->where('structurable_id', $id)
            ->where('position', 'Kaprodi')
            ->where('is_active', true)
            ->first();

        $this->head_start_date = $activeHistory ? $activeHistory->start_date->format('Y-m-d') : date('Y-m-d');

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function showHistory($id)
    {
        $this->historyList = StructureHistory::where('structurable_type', StudyProgram::class)
            ->where('structurable_id', $id)
            ->where('position', 'Kaprodi')
            ->orderBy('start_date', 'desc')
            ->get();

        $this->isHistoryOpen = true;
    }

    public function store()
    {
        $this->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'code' => 'required|unique:study_programs,code,' . $this->prodi_id,
            'name' => 'required',
            'degree' => 'required',
            'head_start_date' => 'nullable|date',
            'is_package' => 'boolean', // Validasi boolean
        ]);

        $oldProdi = null;
        if ($this->isEditMode && $this->prodi_id) {
            $oldProdi = StudyProgram::where('id', $this->prodi_id)->first();
        }

        $prodi = StudyProgram::updateOrCreate(['id' => $this->prodi_id], [
            'faculty_id' => $this->faculty_id,
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'degree' => $this->degree,
            'total_credits' => $this->total_credits ?? 144,
            'is_package' => $this->is_package, // <--- Simpan
            'head_name' => $this->head_name,
            'head_nip' => $this->head_nip,
        ]);

        // ... Logic Riwayat Kaprodi (Sama seperti sebelumnya) ...
        $currentHistory = StructureHistory::where('structurable_type', StudyProgram::class)
            ->where('structurable_id', $prodi->id)
            ->where('position', 'Kaprodi')
            ->where('is_active', true)
            ->first();

        $nameChanged = $this->isEditMode && $oldProdi && ($oldProdi->head_name !== $this->head_name);
        $newEntry = !$this->isEditMode && $this->head_name;

        if ($nameChanged || $newEntry) {
            if ($currentHistory) {
                $currentHistory->update(['is_active' => false, 'end_date' => $this->head_start_date]);
            }
            if ($this->head_name) {
                StructureHistory::create([
                    'structurable_type' => StudyProgram::class,
                    'structurable_id' => $prodi->id,
                    'position' => 'Kaprodi',
                    'official_name' => $this->head_name,
                    'official_nip' => $this->head_nip,
                    'start_date' => $this->head_start_date,
                    'is_active' => true,
                ]);
            }
        } elseif ($currentHistory) {
            $currentHistory->update(['official_nip' => $this->head_nip, 'start_date' => $this->head_start_date]);
        } elseif ($this->head_name) {
            StructureHistory::create([
                'structurable_type' => StudyProgram::class,
                'structurable_id' => $prodi->id,
                'position' => 'Kaprodi',
                'official_name' => $this->head_name,
                'official_nip' => $this->head_nip,
                'start_date' => $this->head_start_date,
                'is_active' => true,
            ]);
        }

        session()->flash('message', 'Data Prodi berhasil disimpan.');
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function delete($id)
    {
        $p = StudyProgram::find($id);
        if($p) $p->delete();
        session()->flash('message', 'Prodi dihapus.');
    }

    private function resetFields()
    {
        $this->reset([
            'prodi_id', 'faculty_id', 'code', 'name', 'degree', 'head_name', 'head_nip'
        ]);
        $this->head_start_date = date('Y-m-d');
        $this->total_credits = 144;
        $this->is_package = false; // Reset default
    }
}