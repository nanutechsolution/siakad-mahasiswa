<?php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use App\Models\Student;
use App\Models\Course;
use App\Models\Classroom;
use App\Models\StudyPlan;
use App\Models\StudyProgram;
use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\DB;

class KrsGenerate extends Component
{
    // Form Inputs
    public $prodi_id;
    public $entry_year;
    public $target_semester = 1;
    public $class_name = 'A'; // Default kita masukkan ke Kelas A

    // Data Penunjang
    public $active_period;
    public $preview_students_count = 0;
    public $preview_courses_count = 0;

    public function mount()
    {
        $this->active_period = AcademicPeriod::where('is_active', true)->first();
        $this->entry_year = date('Y'); // Default tahun ini
    }

    // Hitung estimasi sebelum eksekusi (biar admin gak kaget)
    public function updated($propertyName)
    {
        $this->calculatePreview();
    }

    public function calculatePreview()
    {
        if ($this->prodi_id && $this->entry_year && $this->target_semester) {
            $this->preview_students_count = Student::where('study_program_id', $this->prodi_id)
                ->where('entry_year', $this->entry_year)
                ->where('status', 'A') // Hanya yang aktif
                ->count();

            $this->preview_courses_count = Course::where('study_program_id', $this->prodi_id)
                ->where('semester_default', $this->target_semester)
                ->where('is_active', true)
                ->count();
        }
    }

    public function generate()
    {
        $this->validate([
            'prodi_id' => 'required',
            'entry_year' => 'required|numeric',
            'target_semester' => 'required|numeric',
            'class_name' => 'required',
        ]);

        if (!$this->active_period) {
            session()->flash('error', 'Tidak ada semester aktif!');
            return;
        }

        DB::transaction(function () {
            // 1. Ambil Mahasiswa Target
            $students = Student::where('study_program_id', $this->prodi_id)
                ->where('entry_year', $this->entry_year)
                ->where('status', 'A')
                ->get();

            // 2. Ambil Matkul Target
            $courses = Course::where('study_program_id', $this->prodi_id)
                ->where('semester_default', $this->target_semester)
                ->where('is_active', true)
                ->get();

            $successCount = 0;
            $failCount = 0;

            foreach ($students as $student) {
                foreach ($courses as $course) {

                    // 3. Cari Kelas yang sesuai (Misal: Kelas A untuk matkul ini)
                    $classroom = Classroom::where('academic_period_id', $this->active_period->id)
                        ->where('course_id', $course->id)
                        ->where('name', $this->class_name) // Cari kelas spesifik (A/B/C)
                        ->first();

                    if ($classroom) {
                        // 4. Masukkan ke KRS (APPROVED langsung)
                        $plan = StudyPlan::firstOrCreate([
                            'student_id' => $student->id,
                            'classroom_id' => $classroom->id,
                            'academic_period_id' => $this->active_period->id
                        ], [
                            'status' => 'APPROVED'
                        ]);

                        // Update kuota jika barus aja dibuat
                        if ($plan->wasRecentlyCreated) {
                            $classroom->increment('enrolled');
                        }
                    } else {
                        $failCount++; // Kelas belum dibuka admin
                    }
                }
                $successCount++;
            }

            if ($failCount > 0) {
                session()->flash('warning', "Berhasil proses $successCount mahasiswa. Tapi ada $failCount mata kuliah gagal masuk karena Kelas '$this->class_name' belum dibuka di menu Penjadwalan.");
            } else {
                session()->flash('message', "Sukses! Paket Semester $this->target_semester berhasil digenerate untuk $successCount mahasiswa.");
            }
        });
    }

    public function render()
    {
        return view('livewire.admin.academic.krs-generate', [
            'prodis' => StudyProgram::all()
        ])->layout('layouts.admin');
    }
}
