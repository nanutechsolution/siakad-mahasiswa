<?php

namespace App\Models;

use App\Enums\KrsStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class StudyPlan extends Model
{
    use HasUlids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'student_id',
        'academic_period_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => KrsStatus::class,
    ];

    /**
     * Relasi ke item mata kuliah (Detail KRS)
     */
    public function details(): HasMany
    {
        return $this->hasMany(StudyPlanDetail::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Alias untuk kompatibilitas code lama
     */
    public function academic_period(): BelongsTo
    {
        return $this->academicPeriod();
    }
}