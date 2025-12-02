<?php

namespace App\Livewire\Admin\Master;

use App\Models\StudyProgram;
use App\Models\StructureHistory; // <--- Import Model History
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
    public $head_name, $head_nip;
    public $head_start_date; // <--- Tambahan Field Tanggal Menjabat

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
        $this->head_name = $prodi->head_name;
        $this->head_nip = $prodi->head_nip;
        
        // Ambil Tanggal Mulai Menjabat dari History Aktif
        $activeHistory = StructureHistory::where('structurable_type', StudyProgram::class)
            ->where('structurable_id', $id)
            ->where('position', 'Kaprodi')
            ->where('is_active', true)
            ->first();

        // Jika ada history, ambil tanggalnya. Jika tidak, default hari ini.
        $this->head_start_date = $activeHistory ? $activeHistory->start_date->format('Y-m-d') : date('Y-m-d');

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    // --- FITUR RIWAYAT KAPRODI ---
    public function showHistory($id)
    {
        // Ambil data riwayat khusus untuk Prodi ini dengan jabatan 'Kaprodi'
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
            'head_start_date' => 'nullable|date', // Validasi Tanggal
        ]);

        // 1. Ambil Data Lama (Untuk cek apakah Kaprodi berubah)
        $oldProdi = null;
        if ($this->isEditMode) {
            $oldProdi = StudyProgram::find($this->prodi_id);
        }

        // 2. Simpan / Update Data Utama
        $prodi = StudyProgram::updateOrCreate(['id' => $this->prodi_id], [
            'faculty_id' => $this->faculty_id,
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'degree' => $this->degree,
            'head_name' => $this->head_name,
            'head_nip' => $this->head_nip,
        ]);

        // 3. LOGIC RIWAYAT
        
        // Cari history aktif saat ini
        $currentHistory = StructureHistory::where('structurable_type', StudyProgram::class)
            ->where('structurable_id', $prodi->id)
            ->where('position', 'Kaprodi')
            ->where('is_active', true)
            ->first();

        $isNameChanged = ($this->isEditMode && $oldProdi->head_name !== $this->head_name);
        $isNewCreation = (!$this->isEditMode && $this->head_name);

        if ($isNameChanged || $isNewCreation) {
            // A. GANTI PEJABAT BARU
            
            // Tutup masa jabatan pejabat lama
            if ($currentHistory) {
                $currentHistory->update([
                    'is_active' => false, 
                    'end_date' => $this->head_start_date // Tanggal selesai = Tanggal mulai pejabat baru
                ]);
            }

            // Buat record pejabat baru
            if ($this->head_name) {
                StructureHistory::create([
                    'structurable_type' => StudyProgram::class,
                    'structurable_id' => $prodi->id,
                    'position' => 'Kaprodi',
                    'official_name' => $this->head_name,
                    'official_nip' => $this->head_nip,
                    'start_date' => $this->head_start_date, // Pakai input tanggal
                    'is_active' => true,
                ]);
            }
        } elseif ($currentHistory) {
            // B. PEJABAT SAMA (Hanya Edit Data / Revisi Tanggal)
            $currentHistory->update([
                'official_nip' => $this->head_nip,
                'start_date' => $this->head_start_date
            ]);
        } elseif ($this->head_name) {
            // C. KASUS DATA LAMA (Ada nama di Prodi tapi belum ada di History) -> Buatkan History
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
        StudyProgram::find($id)->delete();
        session()->flash('message', 'Prodi dihapus.');
    }

    private function resetFields()
    {
        $this->reset([
            'prodi_id', 'faculty_id', 'code', 'name', 'degree', 'head_name', 'head_nip'
        ]);
        $this->head_start_date = date('Y-m-d'); // Default Hari Ini
    }
}