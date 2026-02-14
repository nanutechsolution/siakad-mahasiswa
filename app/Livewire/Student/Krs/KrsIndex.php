<?php

namespace App\Livewire\Student\Krs;

use App\Models\AcademicPeriod;
use App\Models\CourseClass;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\CurriculumCourse;
use App\Traits\WithToast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class KrsIndex extends Component
{
    use WithToast;

    public $active_period;
    public $student;

    // Data Tampilan
    public $search = '';
    public $available_classes = [];
    public $selected_details = [];
    public $total_sks = 0;
    public $max_sks = 24;
    public $semester_mhs = 1;

    // State Validasi
    public $is_locked = false;
    public $lock_message = '';
    public $current_plan; // Model StudyPlan (Header)

    public function mount()
    {
        $this->student = Auth::user()->student;
        $this->active_period = AcademicPeriod::where('is_active', true)->first();

        if ($this->active_period && $this->student) {
            $this->checkRegistrationStatus();
            $this->calculateStudentSemester();
            $this->determineMaxSks();
            
            if (!$this->is_locked) {
                $this->loadData();
            } else {
                $this->loadSelectedOnly();
            }
        }
    }

    public function updatedSearch()
    {
        $this->loadData();
    }

    private function checkRegistrationStatus()
    {
        // 1. Cek Status Aktif
        if ($this->student->status !== 'active') {
            $this->is_locked = true;
            $this->lock_message = 'Status akademik Anda tidak aktif.';
            return;
        }

        // 2. Cek Pembayaran (Contoh sederhana)
        $unpaid = $this->student->billings()
            ->where('academic_period_id', $this->active_period->id)
            ->where('status', 'unpaid')
            ->exists();

        if ($unpaid) {
            $this->is_locked = true;
            $this->lock_message = 'Silakan lunasi tagihan semester ini terlebih dahulu.';
            return;
        }

        // 3. Cek Dosen Wali
        if (!$this->student->academic_advisor_id) {
            $this->is_locked = true;
            $this->lock_message = 'Anda belum memiliki Dosen Wali.';
            return;
        }
    }

    private function calculateStudentSemester()
    {
        $entryYear = (int) $this->student->entry_year;
        $currentYear = (int) substr($this->active_period->code, 0, 4);
        $periodType = (int) substr($this->active_period->code, -1); // 1 Ganjil, 2 Genap

        $this->semester_mhs = (($currentYear - $entryYear) * 2) + ($periodType == 1 ? 1 : 2);
        if ($this->semester_mhs < 1) $this->semester_mhs = 1;
    }

    private function determineMaxSks()
    {
        // Logic SKS berdasarkan IPK sebelumnya bisa ditaruh di sini
        $this->max_sks = 24; 
        if (Str::endsWith($this->active_period->code, '3')) $this->max_sks = 9;
    }

    public function loadData()
    {
        $this->loadSelectedOnly();

        $takenClassIds = $this->selected_details->pluck('course_class_id')->toArray();

        // Ambil kelas yang tersedia di prodi mhs & periode aktif
        $this->available_classes = CourseClass::with(['course', 'classSchedules'])
            ->where('academic_period_id', $this->active_period->id)
            ->where('is_active', true)
            ->whereHas('course', function ($q) {
                $q->where('study_program_id', $this->student->study_program_id);
                if ($this->search) {
                    $q->where(fn($sub) => $sub->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%"));
                }
            })
            ->whereNotIn('id', $takenClassIds)
            ->get();
    }

    public function loadSelectedOnly()
    {
        $this->current_plan = StudyPlan::with(['details.courseClass.course', 'details.courseClass.classSchedules'])
            ->where('student_id', $this->student->id)
            ->where('academic_period_id', $this->active_period->id)
            ->first();

        if ($this->current_plan) {
            $this->selected_details = $this->current_plan->details;
            $this->total_sks = $this->selected_details->sum(fn($d) => $d->courseClass->course->credit_total ?? 0);
        } else {
            $this->selected_details = collect();
            $this->total_sks = 0;
        }
    }

    public function takeClass($classId)
    {
        if ($this->is_locked || !$this->active_period->allow_krs) return;

        $class = CourseClass::with('course')->findOrFail($classId);

        // 1. Validasi SKS
        if (($this->total_sks + $class->course->credit_total) > $this->max_sks) {
            return $this->alertError('Batas SKS terlampaui.');
        }

        // 2. Validasi Kuota
        if ($class->enrolled_count >= $class->quota) {
            return $this->alertError('Kuota kelas penuh.');
        }

        // 3. Validasi Bentrok Jadwal
        if ($this->hasConflict($class)) {
            return $this->alertError('Jadwal bentrok.');
        }

        // 4. Proses Simpan (Header & Detail)
        if (!$this->current_plan) {
            $this->current_plan = StudyPlan::create([
                'id' => (string) Str::ulid(),
                'student_id' => $this->student->id,
                'academic_period_id' => $this->active_period->id,
                'status' => 'draft'
            ]);
        }

        StudyPlanDetail::create([
            'id' => (string) Str::ulid(),
            'study_plan_id' => $this->current_plan->id,
            'course_class_id' => $class->id,
        ]);

        $class->increment('enrolled_count');
        $this->loadData();
        $this->alertSuccess('Mata kuliah ditambahkan.');
    }

    public function dropClass($detailId)
    {
        if ($this->is_locked || !$this->active_period->allow_krs) return;

        $detail = StudyPlanDetail::with('courseClass')->findOrFail($detailId);
        
        if ($detail->studyPlan->status !== 'draft') {
            return $this->alertError('KRS sudah diajukan, tidak bisa diubah.');
        }

        $detail->courseClass->decrement('enrolled_count');
        $detail->delete();

        $this->loadData();
        $this->alertSuccess('Mata kuliah dihapus.');
    }

    public function ajukanKrs()
    {
        if (!$this->current_plan || $this->selected_details->isEmpty()) return;

        $this->current_plan->update(['status' => 'submitted']);
        $this->loadData();
        $this->alertSuccess('KRS berhasil diajukan.');
    }

    private function hasConflict($newClass)
    {
        $newSchedules = $newClass->classSchedules;
        foreach ($this->selected_details as $detail) {
            foreach ($detail->courseClass->classSchedules as $existingSch) {
                foreach ($newSchedules as $newSch) {
                    if ($newSch->day_of_week == $existingSch->day_of_week) {
                        if ($newSch->start_time < $existingSch->end_time && $newSch->end_time > $existingSch->start_time) {
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