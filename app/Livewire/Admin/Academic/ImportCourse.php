<?php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\StudyProgram;
use Illuminate\Support\Facades\DB;

class ImportCourse extends Component
{
    use WithFileUploads, WithPagination;

    public $file;
    public $import_logs = [];
    public $show_results = false;
    
    // Filter & Search untuk tabel monitoring di bawah
    public $search = '';
    public $filter_prodi = '';

    public function updatingSearch() { $this->resetPage(); }

    /**
     * Jalankan proses import dari CSV
     */
    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $path = $this->file->getRealPath();
        $file = fopen($path, 'r');
        $row = 0;
        
        $this->import_logs = [];
        $successCount = 0;
        $failCount = 0;

        $prodis = StudyProgram::all();

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $row++;
                if ($row == 1) continue; // Skip Header

                /* FORMAT KOLOM CSV:
                   0: Kode Prodi, 1: Kode Matkul, 2: Nama Matkul, 3: SKS, 4: SMT, 5: Wajib(Y/T), 
                   6: Prasyarat (Format: KODE:GRADE;KODE:GRADE, misal: TI101:B;TI201:C)
                */
                $prodiCode   = trim($data[0] ?? '');
                $courseCode  = trim($data[1] ?? '');
                $courseName  = trim($data[2] ?? '');
                $credits     = intval($data[3] ?? 0);
                $semester    = intval($data[4] ?? 1);
                $isMandatory = (strtoupper($data[5] ?? 'Y') == 'Y');
                $prereqData  = trim($data[6] ?? '');

                if (empty($courseCode) || empty($courseName)) {
                    $this->import_logs[] = "Baris $row: Gagal - Data Kode/Nama kosong.";
                    $failCount++;
                    continue;
                }

                $prodi = $prodis->where('code', strtoupper($prodiCode))->first();
                if (!$prodi) {
                    $this->import_logs[] = "Baris $row: Gagal - Prodi '$prodiCode' tidak ditemukan.";
                    $failCount++;
                    continue;
                }

                // 1. Simpan/Update Matakuliah Utama
                $course = Course::updateOrCreate(
                    [
                        'code' => strtoupper($courseCode),
                        'study_program_id' => $prodi->id
                    ],
                    [
                        'name' => $courseName,
                        'credit_total' => $credits,
                        'semester_default' => $semester,
                        'is_mandatory' => $isMandatory,
                        'is_active' => true
                    ]
                );

                // 2. Proses Prasyarat & Nilai Minimal (Logika baru sesuai Canvas)
                if (!empty($prereqData)) {
                    $pairs = explode(';', $prereqData);
                    $syncData = [];

                    foreach ($pairs as $pair) {
                        // Pisahkan antara Kode dan Grade (Contoh TI101:B)
                        $parts = explode(':', $pair);
                        $pCode = strtoupper(trim($parts[0]));
                        $pGrade = strtoupper(trim($parts[1] ?? 'C')); // Default C jika tidak ditulis

                        // Cari Matkul Prasyarat di DB
                        $pMatkul = Course::where('code', $pCode)->first();
                        
                        if ($pMatkul) {
                            $syncData[$pMatkul->id] = ['min_grade' => $pGrade];
                        } else {
                            $this->import_logs[] = "Baris $row: Warning - Matkul Prasyarat '$pCode' belum ada di sistem.";
                        }
                    }
                    
                    // Sinkronisasi tabel pivot dengan data nilai minimal
                    $course->prerequisites()->sync($syncData);
                } else {
                    $course->prerequisites()->detach();
                }

                $successCount++;
            }

            DB::commit();
            session()->flash('message', "Import Berhasil! $successCount matkul diproses.");
            $this->show_results = true;

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Kesalahan sistem: ' . $e->getMessage());
        }

        fclose($file);
        $this->file = null;
    }

    public function render()
    {
        $courses = Course::with(['study_program', 'prerequisites'])
            ->when($this->search, function($q) {
                $q->where('name', 'like', '%'.$this->search.'%')->orWhere('code', 'like', '%'.$this->search.'%');
            })
            ->when($this->filter_prodi, function($q) {
                $q->where('study_program_id', $this->filter_prodi);
            })
            ->orderBy('study_program_id')
            ->orderBy('semester_default')
            ->paginate(10);

        return view('livewire.admin.academic.import-course', [
            'courses' => $courses,
            'prodis' => StudyProgram::all()
        ])->layout('layouts.admin');
    }
}