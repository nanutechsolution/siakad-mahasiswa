<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    // tabel curriculums
    protected $table = 'curriculums';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Kurikulum milik Prodi
     */
    public function studyProgram()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    /**
     * Daftar matkul dalam kurikulum ini
     */
    public function curriculumCourses()
    {
        return $this->hasMany(CurriculumCourse::class);
    }
}
