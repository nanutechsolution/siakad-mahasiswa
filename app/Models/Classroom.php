<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Classroom extends Model
{
    use HasUlids;

    protected $fillable = [
        'academic_period_id',
        'course_id',
        'lecturer_id',
        'name',
        'quota',
        'enrolled',
        'is_open'
    ];

    // Relasi
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }
    public function academic_period()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
    // Relasi ke KRS/Rencana Studi (untuk melihat siapa saja yg ambil kelas ini)
    public function study_plans()
    {
        return $this->hasMany(StudyPlan::class);
    }
}
