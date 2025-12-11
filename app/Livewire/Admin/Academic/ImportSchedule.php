<?php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\Lecturer;
use App\Models\Schedule;
use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportSchedule extends Component
{
    use WithFileUploads;

    public $file;
    public $active_period;
    public $import_logs = []; // Untuk menampilkan hasil import (Sukses/Gagal)

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
    }

    public function import()
    {
        $this->validate([
            'file' => 'required|mimes:csv,txt|max:2048', // Max 2MB, CSV only
        ]);

        if (!$this->active_period) {
            session()->flash('error', 'Tidak ada semester aktif. Harap atur periode akademik terlebih dahulu.');
            return;
        }

        $path = $this->file->getRealPath();
        $file = fopen($path, 'r');
        $row = 0;
        
        $this->import_logs = []; // Reset log
        $successCount = 0;

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $row++;
                // Skip Header (Baris 1)
                if ($row == 1) continue;

                // FORMAT CSV YANG DIHARAPKAN:
                // 0: Kode Matkul (TI-101)
                // 1: Nama Kelas (A)
                // 2: NIDN Dosen (00112233)
                // 3: Hari (Senin)
                // 4: Jam Mulai (08:00)
                // 5: Jam Selesai (10:00)
                // 6: Ruangan (R-101)
                // 7: Kuota (40)

                $kodeMatkul = trim($data[0] ?? '');
                $namaKelas = trim($data[1] ?? '');
                $nidnDosen = trim($data[2] ?? '');
                $hari = trim($data[3] ?? '');
                $jamMulai = trim($data[4] ?? '');
                $jamSelesai = trim($data[5] ?? '');
                $ruangan = trim($data[6] ?? '');
                $kuota = intval($data[7] ?? 40);

                // Validasi Data Dasar
                if (empty($kodeMatkul) || empty($namaKelas)) {
                    $this->import_logs[] = "Baris $row: Gagal - Kode Matkul atau Nama Kelas kosong.";
                    continue;
                }

                // 1. Cari Mata Kuliah
                $course = Course::where('code', $kodeMatkul)->first();
                if (!$course) {
                    $this->import_logs[] = "Baris $row: Gagal - Matkul '$kodeMatkul' tidak ditemukan.";
                    continue;
                }

                // 2. Cari Dosen (Optional, jika kosong kelas tanpa dosen)
                $lecturerId = null;
                if (!empty($nidnDosen)) {
                    $lecturer = Lecturer::where('nidn', $nidnDosen)->first();
                    if ($lecturer) {
                        $lecturerId = $lecturer->id;
                    } else {
                        $this->import_logs[] = "Baris $row: Warning - Dosen NIDN '$nidnDosen' tidak ditemukan. Kelas dibuat tanpa dosen.";
                    }
                }

                // 3. Buat/Update Kelas (Classroom)
                $classroom = Classroom::firstOrCreate(
                    [
                        'academic_period_id' => $this->active_period->id,
                        'course_id' => $course->id,
                        'name' => strtoupper($namaKelas),
                    ],
                    [
                        'lecturer_id' => $lecturerId,
                        'quota' => $kuota,
                        'is_open' => true
                    ]
                );
                
                // Update dosen jika sebelumnya null atau ingin di-override (opsional)
                if ($lecturerId) {
                    $classroom->update(['lecturer_id' => $lecturerId]);
                }

                // 4. Buat Jadwal (Schedule)
                if (!empty($hari) && !empty($jamMulai) && !empty($jamSelesai)) {
                    // Cek duplikasi jadwal di kelas yang sama agar tidak double insert kalau di-upload ulang
                    Schedule::firstOrCreate(
                        [
                            'classroom_id' => $classroom->id,
                            'day' => ucfirst(strtolower($hari)),
                            'start_time' => $jamMulai,
                            'end_time' => $jamSelesai,
                        ],
                        [
                            'room_name' => $ruangan
                        ]
                    );
                }

                $successCount++;
            }

            DB::commit();
            session()->flash('success', "Import selesai! $successCount jadwal berhasil diproses.");

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

        fclose($file);
    }

    public function render()
    {
        return view('livewire.admin.academic.import-schedule')->layout('layouts.admin');
    }
}