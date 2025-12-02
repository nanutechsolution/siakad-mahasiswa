<?php

namespace App\Models;

use App\Enums\KrsStatus;
use Illuminate\Database\Eloquent\Model;

class StudyPlan extends Model
{
    protected $fillable = [
        'student_id',
        'classroom_id',
        'academic_period_id',
        'status',
        'score_number',
        'grade_letter',
        'grade_point'
    ];
    protected $casts = [
        'status' => KrsStatus::class,
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academic_period()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }
}
