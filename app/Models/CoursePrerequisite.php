<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class CoursePrerequisite extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    use HasFactory;

    protected $fillable = [
        'curriculum_course_id',
        'prerequisite_curriculum_course_id',
        'min_grade',
    ];

    /**
     * Boot function to auto-generate ULID
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function curriculumCourse()
    {
        return $this->belongsTo(CurriculumCourse::class, 'curriculum_course_id');
    }

    public function prerequisite()
    {
        return $this->belongsTo(CurriculumCourse::class, 'prerequisite_curriculum_course_id');
    }
}
