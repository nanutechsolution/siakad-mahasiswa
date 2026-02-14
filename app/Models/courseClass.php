<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CourseClass extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function classSchedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }
    
    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'class_lecturers')
                    ->withPivot(['is_primary', 'can_input_grade']);
    }
    
    // Helper untuk mengambil dosen utama (satu saja)
    public function lecturer()
    {
        return $this->hasOneThrough(
            Lecturer::class,
            ClassLecturer::class,
            'course_class_id', // FK di pivot
            'id', // PK di lecturer
            'id', // PK di course_classes
            'lecturer_id' // FK di pivot
        )->where('is_primary', true);
    }
}