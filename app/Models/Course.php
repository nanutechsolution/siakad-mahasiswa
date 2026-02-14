<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
            if (!empty($model->code)) {
                $model->code = strtoupper($model->code);
            }
        });

        static::updating(function ($model) {
            if (!empty($model->code)) {
                $model->code = strtoupper($model->code);
            }
        });
    }
    // Matkul milik Prodi
    public function study_program()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    /**
     * Relasi ke tabel curriculum_courses
     * (1 matkul bisa muncul di banyak kurikulum)
     */
    public function curriculumCourses()
    {
        return $this->hasMany(CurriculumCourse::class);
    }
}
