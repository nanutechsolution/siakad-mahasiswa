<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model

{

    use HasFactory, HasUlids;

    protected $guarded = ['id'];

    protected $keyType = 'string';
    public $incrementing = false;

     protected $casts = [
        'dob' => 'date',
        'father_dob' => 'date',
        'mother_dob' => 'date',
        'guardian_dob' => 'date',
        'is_kps_recipient' => 'boolean',
    ];


    public function study_plans()
    {
        return $this->hasMany(StudyPlan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function study_program()
    {
        return $this->belongsTo(StudyProgram::class);
    }
    public function academic_advisor()
    {
        return $this->belongsTo(Lecturer::class, 'academic_advisor_id');
    }
    public function billings()
    {
        return $this->hasMany(Billing::class);
    }
}
