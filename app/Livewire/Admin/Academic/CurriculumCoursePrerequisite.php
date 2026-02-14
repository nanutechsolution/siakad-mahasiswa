<?php

namespace App\Livewire\Admin\Academic;

use Livewire\Component;
use App\Models\CurriculumCourse;
use App\Models\CoursePrerequisite;
use Illuminate\Support\Str;

class CurriculumCoursePrerequisite extends Component
{
    public $curriculumCourseId;
    public $currentCourse; // Data course yang sedang diedit
    public $availableCourses = []; // Daftar kandidat prasyarat
    
    // State Form
    public $selectedCourses = []; // [course_id => true]
    public $grades = []; // [course_id => 'C']

    public function mount($curriculum_course)
    {
        // 1. Ambil Data Utama (Fail Fast jika ID salah)
        $this->currentCourse = CurriculumCourse::with(['course', 'curriculum'])
            ->findOrFail($curriculum_course);
        
        $this->curriculumCourseId = $this->currentCourse->id;

        // 2. Load Kandidat (Semua matkul di kurikulum yg sama, kecuali diri sendiri)
        $this->availableCourses = CurriculumCourse::with('course')
            ->where('curriculum_id', $this->currentCourse->curriculum_id)
            ->where('id', '!=', $this->currentCourse->id)
            ->where('semester', '<=', $this->currentCourse->semester) // Logika: Syarat biasanya semester sebelumnya/sama
            ->orderBy('semester')
            ->get();

        // 3. Load Prasyarat Existing
        $existing = CoursePrerequisite::where('curriculum_course_id', $this->curriculumCourseId)->get();

        foreach ($existing as $item) {
            $id = $item->prerequisite_curriculum_course_id;
            $this->selectedCourses[$id] = true;
            $this->grades[$id] = $item->min_grade;
        }
    }

    public function save()
    {
        // 1. Hapus semua prasyarat lama (Wipe & Replace strategy - Paling aman untuk relasi M-to-M custom)
        CoursePrerequisite::where('curriculum_course_id', $this->curriculumCourseId)->delete();

        // 2. Insert baru yang dicentang
        $count = 0;
        foreach ($this->selectedCourses as $prereqId => $isSelected) {
            if ($isSelected) {
                CoursePrerequisite::create([
                    'id' => (string) Str::ulid(),
                    'curriculum_course_id' => $this->curriculumCourseId,
                    'prerequisite_curriculum_course_id' => $prereqId,
                    'min_grade' => $this->grades[$prereqId] ?? 'D', // Default nilai D
                ]);
                $count++;
            }
        }

        session()->flash('message', "Berhasil menyimpan {$count} prasyarat.");
    }

    public function render()
    {
        return view('livewire.admin.academic.curriculum-course-prerequisite')
            ->layout('layouts.admin');
    }
}