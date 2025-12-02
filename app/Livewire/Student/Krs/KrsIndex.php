<?php

namespace App\Livewire\Student\Krs;

use App\Models\AcademicPeriod;
use App\Models\Classroom;
use App\Models\StudyPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Enums\KrsStatus;
use App\Traits\WithToast;

class KrsIndex extends Component
{
    use WithToast;
    public $active_period;
    public $student;

    // Data Tampilan
    public $available_classes = [];
    public $selected_classes = [];
    public $total_sks = 0;
    public $max_sks = 24; // Hardcode dulu, nanti dari IPK

    public function mount()
    {
        // 1. Ambil Mahasiswa Login
        // Kita butuh data student, bukan user.
        $this->student = Auth::user()->student;

        // 2. Cek Semester Aktif & Izin KRS
        $this->active_period = AcademicPeriod::where('is_active', true)->first();

        if ($this->active_period) {
            $this->loadData();
        }
    }

    public function loadData()
    {
        // Ambil kelas yang sudah diambil (Keranjang Saya)
        $taken = StudyPlan::with(['classroom.course', 'classroom.schedules'])
            ->where('student_id', $this->student->id)
            ->where('academic_period_id', $this->active_period->id)
            ->get();

        $this->selected_classes = $taken;
        $this->total_sks = $taken->sum(fn($krs) => $krs->classroom->course->credit_total);

        // Ambil kelas yang TERSEDIA (Hanya prodi dia, dan belum diambil)
        $takenClassIds = $taken->pluck('classroom_id')->toArray();

        $this->available_classes = Classroom::with(['course', 'lecturer', 'schedules'])
            ->where('academic_period_id', $this->active_period->id)
            ->where('is_open', true)
            ->whereHas('course', function ($q) {
                // Filter Matkul sesuai Prodi Mahasiswa
                $q->where('study_program_id', $this->student->study_program_id);
            })
            ->whereNotIn('id', $takenClassIds) // Jangan munculkan yg sudah diambil
            ->get();
    }

    public function takeClass($classId)
    {
        // VALIDASI 1: Apakah masa KRS buka?
        if (!$this->active_period->allow_krs) {
            $this->alertError('Masa pengisian KRS sudah ditutup.');
            return;
        }

        $class = Classroom::with(['course', 'schedules'])->find($classId);

        // VALIDASI 2: Cek Kuota
        if ($class->enrolled >= $class->quota) {
            $this->alertError('Kelas penuh! Cari kelas lain.');
            return;
        }

        // VALIDASI 3: Cek SKS Limit
        if (($this->total_sks + $class->course->credit_total) > $this->max_sks) {
            $this->alertError('SKS melebihi batas maksimal (' . $this->max_sks . ').');
            return;
        }

        // VALIDASI 4: Cek Bentrok Jadwal
        // Ambil jadwal kelas yg mau diambil
        foreach ($class->schedules as $newSch) {
            // Bandingkan dengan semua kelas yg sudah diambil
            foreach ($this->selected_classes as $takenKrs) {
                foreach ($takenKrs->classroom->schedules as $takenSch) {
                    // Jika Harinya Sama
                    if ($newSch->day == $takenSch->day) {
                        // Cek Irisan Waktu
                        // (Start A < End B) AND (End A > Start B)
                        if ($newSch->start_time < $takenSch->end_time && $newSch->end_time > $takenSch->start_time) {
                            $this->alertError("BENTROK JADWAL! Matkul ini bentrok dengan " . $takenKrs->classroom->course->name);
                            return;
                        }
                    }
                }
            }
        }

        // LOLOS VALIDASI -> SIMPAN
        StudyPlan::create([
            'student_id' => $this->student->id,
            'classroom_id' => $classId,
            'academic_period_id' => $this->active_period->id,
            'status' => KrsStatus::DRAFT,
        ]);

        // Update Counter Kuota (Increment)
        $class->increment('enrolled');

        $this->alertSuccess('Berhasil mengambil mata kuliah.');
        $this->loadData(); // Refresh tampilan
    }

    public function dropClass($planId)
    {
        if (!$this->active_period->allow_krs) {
            $this->alertError('Masa KRS tutup. Tidak bisa membatalkan.');
            return;
        }

        $plan = StudyPlan::find($planId);
        // Cek apakah statusnya DRAFT. Jika bukan DRAFT, tolak penghapusan.
        if ($plan->status !== KrsStatus::DRAFT) {
            $this->alertError('Gagal! Mata kuliah yang sudah diajukan atau disetujui tidak dapat dihapus.');
            return;
        }
        // Update Counter Kuota (Decrement)
        $plan->classroom->decrement('enrolled');

        $plan->delete();

        $this->alertSuccess('Mata kuliah dibatalkan.');
        $this->loadData();
    }

    public function ajukanKrs()
    {
        // Fix: Gunakan collect() agar aman jika properti masih dianggap array oleh static analysis
        if (collect($this->selected_classes)->isEmpty()) {
            $this->alertError('Keranjang KRS masih kosong.');
            return;
        }

        // Update semua status DRAFT menjadi SUBMITTED (Diajukan)
        // khusus untuk mahasiswa ini di semester ini
        // PERBAIKAN: Gunakan 'SUBMITTED' sesuai Enum di Database, bukan 'PENDING'
        StudyPlan::where('student_id', $this->student->id)
            ->where('academic_period_id', $this->active_period->id)
            ->where('status', KrsStatus::DRAFT)
            ->update(['status' => KrsStatus::SUBMITTED]);

        $this->alertSuccess('KRS Berhasil diajukan! Harap tunggu validasi Dosen Wali.');

        // Refresh data agar tombol berubah jadi "Menunggu Persetujuan"
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.student.krs.krs-index')->layout('layouts.student');
    }
}
