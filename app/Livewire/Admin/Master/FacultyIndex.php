<?php

namespace App\Livewire\Admin\Master;

use App\Models\Faculty;
use App\Models\StructureHistory;
use Livewire\Component;
use Livewire\WithPagination;

class FacultyIndex extends Component
{
    use WithPagination;

    // State
    public $search = '';
    public $isModalOpen = false;
    public $isEditMode = false;

    // Form Fields
    public $faculty_id;
    public $code;       // Contoh: FT
    public $name;       // Contoh: Fakultas Teknik
    public $dean_name;  // Contoh: Dr. Eng. Budi

    public $historyList = [];
    public $isHistoryOpen = false;
    public function showHistory($id)
    {
        $faculty = Faculty::find($id);
        // Ambil data dari tabel structure_histories
        $this->historyList = StructureHistory::where('structurable_type', Faculty::class)
            ->where('structurable_id', $id)
            ->orderBy('start_date', 'desc')
            ->get();

        $this->isHistoryOpen = true;
    }
    public function render()
    {
        $faculties = Faculty::query()
            ->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('code', 'like', '%' . $this->search . '%')
            ->orWhere('dean_name', 'like', '%' . $this->search . '%')
            ->orderBy('code', 'asc')
            ->paginate(10);

        return view('livewire.admin.master.faculty-index', [
            'faculties' => $faculties
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
        $faculty = Faculty::findOrFail($id);
        $this->faculty_id = $id;
        $this->code = $faculty->code;
        $this->name = $faculty->name;
        $this->dean_name = $faculty->dean_name;

        $this->isEditMode = true;
        $this->isModalOpen = true;
    }

    public function store()
    {
        $this->validate([
            // Kode harus unik, kecuali milik diri sendiri saat edit
            'code' => 'required|string|max:10|unique:faculties,code,' . $this->faculty_id,
            'name' => 'required|string|max:255',
            'dean_name' => 'nullable|string|max:255',
        ]);

        $oldFaculty = null;
        if ($this->isEditMode) {
            $oldFaculty = Faculty::find($this->faculty_id);
        }

        // 2. Simpan / Update Data Utama
        $faculty = Faculty::updateOrCreate(['id' => $this->faculty_id], [
            'code' => strtoupper($this->code),
            'name' => $this->name,
            'dean_name' => $this->dean_name, // Nama Baru
        ]);

        // 3. LOGIC RIWAYAT (Hanya jika nama Dekan berubah atau Baru)
        if (!$this->isEditMode || ($oldFaculty && $oldFaculty->dean_name !== $this->dean_name)) {

            // A. Matikan Pejabat Lama (Jika ada)
            if ($oldFaculty) {
                StructureHistory::where('structurable_type', Faculty::class)
                    ->where('structurable_id', $faculty->id)
                    ->where('is_active', true)
                    ->where('position', 'Dekan')
                    ->update([
                        'is_active' => false,
                        'end_date' => now(), // Selesai hari ini
                    ]);
            }

            // B. Buat Record Pejabat Baru
            if ($this->dean_name) {
                StructureHistory::create([
                    'structurable_type' => Faculty::class,
                    'structurable_id' => $faculty->id,
                    'position' => 'Dekan',
                    'official_name' => $this->dean_name,
                    'official_nip' => null, // Tambahkan field NIP di form jika perlu
                    'start_date' => now(),
                    'is_active' => true,
                ]);
            }
        }

        session()->flash('message', $this->isEditMode ? 'Fakultas diperbarui.' : 'Fakultas berhasil ditambahkan.');
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function delete($id)
    {
        try {
            $faculty = Faculty::findOrFail($id);
            $faculty->delete();
            session()->flash('message', 'Data Fakultas dihapus.');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus. Mungkin fakultas ini masih memiliki Prodi.');
        }
    }

    private function resetFields()
    {
        $this->reset(['faculty_id', 'code', 'name', 'dean_name']);
    }
}
