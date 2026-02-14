<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CurriculumCourse extends Model

{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    /**
     * PRASYARAT MATKUL (VERSI BENAR)
     */
    public function prerequisites()
    {
        return $this->belongsToMany(
            CurriculumCourse::class,
            'course_prerequisites',
            'curriculum_course_id',
            'prerequisite_curriculum_course_id'
        )->withPivot(['id', 'min_grade'])->withTimestamps();
    }
    /**
     * Matakuliah ini MENJADI SYARAT untuk matakuliah apa?
     * Contoh: "Pemrograman Dasar" dibutuhkan oleh ["Pemrograman Lanjut"]
     */
    public function requiredFor(): BelongsToMany
    {
        return $this->belongsToMany(
            CurriculumCourse::class,
            'course_prerequisites',
            'prerequisite_curriculum_course_id', // Dibalik posisinya
            'curriculum_course_id'
        )
            ->withPivot(['id', 'min_grade'])
            ->withTimestamps();
    }

    public function getSemesterNameAttribute()
    {
        return $this->semester % 2 == 1 ? 'Ganjil' : 'Genap';
    }
}
