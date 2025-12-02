<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Billing extends Model
{
    use HasUlids;

    protected $fillable = [
        'student_id', 'academic_period_id', 'title', 'description', 
        'amount', 'due_date', 'status'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function student() {
        return $this->belongsTo(Student::class);
    }

    public function academic_period() {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }
}