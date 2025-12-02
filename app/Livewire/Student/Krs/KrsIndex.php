<?php

namespace App\Livewire\Student\Krs;

use App\Models\AcademicPeriod;
use App\Models\Classroom;
use App\Models\StudyPlan;
use App\Enums\KrsStatus;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KrsIndex extends Component
{
    public $active_period;
    public $student;

    // Data Tampilan
    public $available_classes = [];
    public $selected_classes = [];
    public $total_sks = 0;
    public $max_sks = 24;

    public $semester_mhs = 1;

    public function mount()
    {
        $this->student = Auth::user()->student;
        $this->active_period = AcademicPeriod::where('is_active', true)->first();

        if ($this->active_period && $this->student) {
            $this->calculateStudentSemester();
            $this->loadData();
        }
    }

    private function calculateStudentSemester()
    {
        $angkatan = (int) $this->student->entry_year;
        $tahun_periode = (int) substr($this->active_period->code, 0, 4);
        $digit_akhir = (int) substr($this->active_period->code, -1);
        $tipe_semester = ($digit_akhir % 2 != 0) ? 1 : 2;

        $this->semester_mhs = (($tahun_periode - $angkatan) * 2) + $tipe_semester;
        if ($this->semester_mhs < 1) $this->semester_mhs = 1;
    }

    public function loadData()
    {
        $taken = StudyPlan::with(['classroom.course', 'classroom.schedules'])
            ->where('student_id', $this->student->id)
            ->where('academic_period_id', $this->active_period->id)
            ->get();

        $this->selected_classes = $taken;
        $this->total_sks = $taken->sum(fn($krs) => $krs->classroom->course->credit_total);
        $takenClassIds = $taken->pluck('classroom_id')->toArray();

        $isPaket = ($this->semester_mhs <= 2);

        $this->available_classes = Classroom::with(['course', 'lecturer', 'schedules'])
            ->where('academic_period_id', $this->active_period->id)
            ->where('is_open', true)
            ->whereHas('course', function ($q) use ($isPaket) {
                $q->where('study_program_id', $this->student->study_program_id);
                if ($isPaket) {
                    $q->where('semester_default', $this->semester_mhs)
                        ->where('is_mandatory', true);
                }
            })
            ->whereNotIn('id', $takenClassIds)
            ->get();
    }

    public function takeClass($classId)
    {
        if (!$this->active_period->allow_krs) {
            session()->flash('error', 'Masa pengisian KRS sudah ditutup.');
            return;
        }

        $class = Classroom::with(['course', 'schedules'])->find($classId);

        if ($class->enrolled >= $class->quota) {
            session()->flash('error', 'Kelas penuh! Cari kelas lain.');
            return;
        }

        if (($this->total_sks + $class->course->credit_total) > $this->max_sks) {
            session()->flash('error', 'SKS melebihi batas maksimal (' . $this->max_sks . ').');
            return;
        }

        // Cek Bentrok (Single)
        if ($this->checkScheduleConflict($class)) {
            return;
        }

        StudyPlan::create([
            'student_id' => $this->student->id,
            'classroom_id' => $classId,
            'academic_period_id' => $this->active_period->id,
            'status' => KrsStatus::DRAFT,
        ]);

        $class->increment('enrolled');

        session()->flash('success', 'Berhasil mengambil mata kuliah.');
        $this->loadData();
    }

    // UPDATE: Logic Take All dengan Validasi Bentrok
    public function takeAll()
    {
        if (!$this->active_period->allow_krs) {
            session()->flash('error', 'Masa pengisian KRS sudah ditutup.');
            return;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($this->available_classes as $class) {

            // Skip jika sudah diambil
            $isTaken = collect($this->selected_classes)->contains('classroom_id', $class->id);
            if ($isTaken) continue;

            // Skip jika kuota penuh
            if ($class->enrolled >= $class->quota) {
                $failCount++;
                continue;
            }

            // Skip jika melebihi SKS
            if (($this->total_sks + $class->course->credit_total) > $this->max_sks) {
                $failCount++;
                continue;
            }

            // Skip jika BENTROK (Penting!)
            // Kita tidak return/stop, tapi continue ke matkul berikutnya
            if ($this->checkScheduleConflict($class, true)) { // true = silent mode (no flash message per item)
                $failCount++;
                continue;
            }

            // Ambil Kelas
            StudyPlan::create([
                'student_id' => $this->student->id,
                'classroom_id' => $class->id,
                'academic_period_id' => $this->active_period->id,
                'status' => KrsStatus::DRAFT,
            ]);

            $class->increment('enrolled');
            $successCount++;

            // Update total SKS sementara agar loop berikutnya valid limit SKS-nya
            $this->total_sks += $class->course->credit_total;

            // Update list kelas yang diambil sementara untuk cek bentrok sesama paket
            $this->selected_classes->push(new StudyPlan(['classroom' => $class]));
        }

        if ($successCount > 0) {
            if ($failCount > 0) {
                session()->flash('warning', "Berhasil mengambil $successCount matkul. Ada $failCount matkul gagal diambil karena Bentrok/Penuh.");
            } else {
                session()->flash('success', "Berhasil mengambil semua ($successCount) mata kuliah paket.");
            }
        } else {
            session()->flash('error', "Gagal mengambil paket. Mungkin jadwal bentrok semua atau SKS penuh.");
        }

        $this->loadData();
    }

    // Helper Cek Bentrok (Dipisah agar bisa dipakai ulang)
    private function checkScheduleConflict($newClass, $silent = false)
    {
        foreach ($newClass->schedules as $newSch) {
            foreach ($this->selected_classes as $takenKrs) {
                // Safety check jika relasi belum ke-load
                if (!$takenKrs->classroom || !$takenKrs->classroom->schedules) continue;

                foreach ($takenKrs->classroom->schedules as $takenSch) {
                    if ($newSch->day == $takenSch->day) {
                        if ($newSch->start_time < $takenSch->end_time && $newSch->end_time > $takenSch->start_time) {
                            if (!$silent) {
                                session()->flash('error', "BENTROK JADWAL! {$newClass->course->name} bentrok dengan {$takenKrs->classroom->course->name} ($newSch->day).");
                            }
                            return true; // Ada bentrok
                        }
                    }
                }
            }
        }
        return false; // Aman
    }

    public function dropClass($planId)
    {
        if (!$this->active_period->allow_krs) {
            session()->flash('error', 'Masa KRS tutup. Tidak bisa membatalkan.');
            return;
        }

        $plan = StudyPlan::find($planId);

        if ($plan->status !== KrsStatus::DRAFT) {
            session()->flash('error', 'Gagal! Mata kuliah yang sudah diajukan tidak dapat dihapus.');
            return;
        }

        $plan->classroom->decrement('enrolled');
        $plan->delete();

        session()->flash('success', 'Mata kuliah dibatalkan.');
        $this->loadData();
    }

    public function ajukanKrs()
    {
        if (collect($this->selected_classes)->isEmpty()) {
            session()->flash('error', 'Keranjang KRS masih kosong.');
            return;
        }

        StudyPlan::where('student_id', $this->student->id)
            ->where('academic_period_id', $this->active_period->id)
            ->where('status', KrsStatus::DRAFT)
            ->update(['status' => KrsStatus::SUBMITTED]);

        session()->flash('success', 'KRS Berhasil diajukan! Harap tunggu validasi Dosen Wali.');
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.student.krs.krs-index')->layout('layouts.student');
    }
}
