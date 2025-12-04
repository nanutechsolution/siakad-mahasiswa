<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Thesis extends Model
{
    use HasUlids;

    protected $fillable = [
        'student_id', 'academic_period_id', 
        'title', 'abstract', 'proposal_file', 'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academic_period()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    // Relasi ke Pembimbing
    public function supervisors()
    {
        return $this->hasMany(ThesisSupervisor::class);
    }

    // Relasi ke Log Bimbingan
    public function logs()
    {
        return $this->hasMany(ThesisLog::class);
    }
}