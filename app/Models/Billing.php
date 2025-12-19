<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Billing extends Model
{
    use HasUlids;

    protected $fillable = [
        'student_id',
        'registrant_id',
        'academic_period_id',
        'title',
        'category',
        'description',
        'amount',
        'due_date',
        'status'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academic_period()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function tuition_rate()
    {
        return $this->belongsTo(TuitionRate::class);
    }

    public function fee_type()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function registrant()
    {
        return $this->belongsTo(Registrant::class);
    }

    // App\Models\Billing.php

    public function getOwnerNameAttribute()
    {
        if ($this->student_id && $this->student) {
            return $this->student->user->name ?? '-';
        }

        if ($this->registrant_id && $this->registrant) {
            return $this->registrant->user->name ?? '-';
        }

        return '-';
    }

    public function getOwnerCodeAttribute()
    {
        if ($this->student_id && $this->student) {
            return $this->student->nim ?? '-';
        }

        if ($this->registrant_id && $this->registrant) {
            return $this->registrant->registration_no ?? '-';
        }

        return '-';
    }


    public function billable()
    {
        return $this->morphTo();
    }
}
