<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'study_program_id',
        'code',
        'name',
        'name_en',
        'group_code',
        'is_mandatory',
        'semester_default',
        'credit_total',
        'credit_theory',
        'credit_practice',
        'is_active',
        'syllabus_path'
    ];

    // Relasi: Matkul ini milik Prodi apa?
    public function study_program()
    {
        return $this->belongsTo(StudyProgram::class);
    }


    // Cara bacanya: "Matkul ini (Web 2) menuntut lulus matkul apa saja?"
    public function prerequisites()
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_course_id')
            ->withPivot('min_grade');
    }

    // Cara bacanya: "Matkul ini (Web 1) menjadi syarat untuk matkul apa saja?"
    public function required_for()
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'prerequisite_course_id', 'course_id');
    }
}
