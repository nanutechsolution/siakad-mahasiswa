<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdomResponse extends Model
{
    protected $fillable = [
        'academic_period_id', // <--- Added this
        'student_id',
        'classroom_id',
        'edom_question_id',
        'score'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function question()
    {
        return $this->belongsTo(EdomQuestion::class, 'edom_question_id');
    }

    public function academic_period()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }
}