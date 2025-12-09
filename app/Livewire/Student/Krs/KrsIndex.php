<?php

namespace App\Livewire\Student\Krs;

use App\Models\AcademicPeriod;
use App\Models\Classroom;
use App\Models\StudyPlan;
use App\Enums\KrsStatus;
use App\Traits\WithToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class KrsIndex extends Component
{
    use WithToast;
    public $active_period;
    public $student;

    // Data Tampilan
    public $search = ''; // <--- 1. TAMBAHKAN DI SINI
    public $available_classes = [];
    public $selected_classes = [];
    public $total_sks = 0;
    public $max_sks = 24;

    public $semester_mhs = 1;

    // State Validasi (Registrasi)
    public $is_locked = false;
    public $lock_message = '';

    public function mount()
    {
        $this->student = Auth::user()->student;
        $this->active_period = AcademicPeriod::where('is_active', true)->first();

        if ($this->active_period && $this->student) {
            $this->checkRegistrationStatus();
            if (substr($this->active_period->code, -1) == '3') {
                $this->max_sks = 9;
            } else {
                // Semester Biasa: Bisa dibuat dinamis berdasarkan IPK lalu
                // Contoh: Jika IPK > 3.00 dapat 24, jika < 2.00 dapat 18
                $this->max_sks = 24;
            }

            if (!$this->is_locked) {
                $this->calculateStudentSemester();
                $this->loadData();
            } else {
                $this->loadSelectedOnly();
            }
        }
    }

    // 2. TAMBAHKAN METHOD INI (Agar auto-refresh saat ngetik)
    public function updatedSearch()
    {
        $this->loadData();
    }

    private function checkRegistrationStatus()
    {
        if ($this->student->status !== 'A') {
            $this->is_locked = true;
            $this->lock_message = 'Status Akademik Anda saat ini adalah NON-AKTIF / CUTI. Silakan lapor BAAK untuk mengaktifkan kembali status mahasiswa.';
            return;
        }

        $unpaid_spp = $this->student->billings()
            ->where('academic_period_id', $this->active_period->id)
            ->where('category', 'SPP')
            ->where('status', '!=', 'PAID')
            ->exists();

        if ($unpaid_spp) {
            $this->is_locked = true;
            $this->lock_message = 'Anda belum melakukan Registrasi Ulang (Pembayaran SPP). Silakan lunasi tagihan di menu Keuangan untuk membuka akses KRS.';
            return;
        }

        if (!$this->student->academic_advisor_id) {
            $this->is_locked = true;
            $this->lock_message = 'Anda belum memiliki Dosen Wali. Silakan hubungi Prodi untuk plotting dosen wali.';
            return;
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
        $this->loadSelectedOnly();

        $takenClassIds = $this->selected_classes->pluck('classroom_id')->toArray();

        $isPaket = ($this->semester_mhs <= 2);
        $history_grades = StudyPlan::with('classroom')
            ->where('student_id', $this->student->id)
            ->whereNotNull('grade_letter')
            ->get()
            ->groupBy('classroom.course_id');
        $this->available_classes = Classroom::with(['course', 'lecturer', 'schedules'])
            ->where('academic_period_id', $this->active_period->id)
            ->where('is_open', true)
            ->whereHas('course', function ($q) use ($isPaket) {
                $q->where('study_program_id', $this->student->study_program_id);

                // 3. TAMBAHKAN LOGIC PENCARIAN DI SINI
                if ($this->search) {
                    $q->where(function ($sub) {
                        $sub->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('code', 'like', '%' . $this->search . '%');
                    });
                }
                // -------------------------------------

                if ($isPaket) {
                    $q->where('semester_default', $this->semester_mhs)
                        ->where('is_mandatory', true);
                }
            })
            ->whereNotIn('id', $takenClassIds)
            ->get()
            ->map(function ($class) use ($history_grades) {
                // Cek apakah pernah ambil dan tidak lulus
                $prev = $history_grades[$class->course_id] ?? null;

                $class->is_retake = false;
                $class->prev_grade = null;

                if ($prev) {
                    $bestGrade = $prev->sortByDesc('grade_point')->first();
                    // Asumsi batas lulus adalah C (2.00)
                    // Jika nilai terbaik < 2.00 atau E, berarti wajib ulang
                    if ($bestGrade->grade_point < 2.00) {
                        $class->is_retake = true;
                        $class->prev_grade = $bestGrade->grade_letter;
                    }
                }

                return $class;
            });
    }

    public function loadSelectedOnly()
    {
        $taken = StudyPlan::with(['classroom.course', 'classroom.schedules'])
            ->where('student_id', $this->student->id)
            ->where('academic_period_id', $this->active_period->id)
            ->get();

        $this->selected_classes = $taken;
        $this->total_sks = $taken->sum(fn($krs) => $krs->classroom->course->credit_total);
    }

    public function takeClass($classId)
    {
        if ($this->is_locked) return;

        if (!$this->active_period->allow_krs) {
            $this->alertError('Masa pengisian KRS sudah ditutup.');
            return;
        }

        $class = Classroom::with(['course', 'schedules'])->find($classId);

        if ($class->enrolled >= $class->quota) {
            $this->alertError('Kelas penuh.');
            return;
        }
        if (($this->total_sks + $class->course->credit_total) > $this->max_sks) {
            $this->alertError('SKS limit.');
            return;
        }
        if ($this->checkScheduleConflict($class)) return;

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

    public function takeAll()
    {
        if ($this->is_locked) return;

        if (!$this->active_period->allow_krs) {
            $this->alertError('Masa pengisian KRS sudah ditutup.');
            return;
        }

        $successCount = 0;
        $failCount = 0;

        $this->selected_classes = collect($this->selected_classes);

        foreach ($this->available_classes as $class) {
            $isTaken = $this->selected_classes->contains('classroom_id', $class->id);
            if ($isTaken) continue;

            if ($class->enrolled >= $class->quota) {
                $failCount++;
                continue;
            }
            if (($this->total_sks + $class->course->credit_total) > $this->max_sks) {
                $failCount++;
                continue;
            }
            if ($this->checkScheduleConflict($class, true)) {
                $failCount++;
                continue;
            }

            StudyPlan::create(['student_id' => $this->student->id, 'classroom_id' => $class->id, 'academic_period_id' => $this->active_period->id, 'status' => KrsStatus::DRAFT]);
            $class->increment('enrolled');
            $successCount++;
            $this->total_sks += $class->course->credit_total;

            $tempPlan = new StudyPlan();
            $tempPlan->classroom_id = $class->id;
            $tempPlan->setRelation('classroom', $class);
            $this->selected_classes->push($tempPlan);
        }

        if ($successCount > 0) {
            if ($failCount > 0) {
                session()->flash('warning', "Berhasil mengambil $successCount matkul. Ada $failCount matkul gagal diambil karena Bentrok/Penuh.");
            } else {
                session()->flash('success', "Berhasil mengambil semua ($successCount) mata kuliah paket.");
            }
        } else {
            $this->alertError("Gagal mengambil paket. Mungkin jadwal bentrok semua atau SKS penuh.");
        }

        $this->loadData();
    }

    public function dropClass($planId)
    {
        if ($this->is_locked) return;

        $plan = StudyPlan::find($planId);
        if (!$this->active_period->allow_krs) {
            $this->alertError('Masa KRS tutup.');
            return;
        }
        if ($plan->status !== KrsStatus::DRAFT) {
            $this->alertError('Gagal hapus. Matkul sudah diajukan/disetujui.');
            return;
        }

        $plan->classroom->decrement('enrolled');
        $plan->delete();

        $this->alertSuccess('Mata kuliah dibatalkan.');
        $this->loadData();
    }

    public function ajukanKrs()
    {
        if ($this->is_locked) return;

        if (collect($this->selected_classes)->isEmpty()) {
            $this->alertError('KRS kosong.');
            return;
        }

        StudyPlan::where('student_id', $this->student->id)
            ->where('academic_period_id', $this->active_period->id)
            ->where('status', KrsStatus::DRAFT)
            ->update(['status' => KrsStatus::SUBMITTED]);

        $this->alertSuccess('KRS Berhasil diajukan! Harap tunggu validasi Dosen Wali.');
        $this->loadData();
    }

    private function checkScheduleConflict($newClass, $silent = false)
    {
        foreach ($newClass->schedules as $newSch) {
            foreach ($this->selected_classes as $takenKrs) {
                if (!$takenKrs->classroom || !$takenKrs->classroom->schedules) continue;
                foreach ($takenKrs->classroom->schedules as $takenSch) {
                    if ($newSch->day == $takenSch->day) {
                        if ($newSch->start_time < $takenSch->end_time && $newSch->end_time > $takenSch->start_time) {
                            if (!$silent) {
                                $this->alertError("BENTROK JADWAL! {$newClass->course->name} bentrok dengan {$takenKrs->classroom->course->name} ($newSch->day).");
                            }
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    public function render()
    {
        return view('livewire.student.krs.krs-index')->layout('layouts.student');
    }
}
