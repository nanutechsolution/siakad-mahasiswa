<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory, HasUlids;

    protected $guarded = ['id'];

    // Konfigurasi ULID
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'dob' => 'date',
        'father_dob' => 'date',
        'mother_dob' => 'date',
        'guardian_dob' => 'date',
        'is_kps_recipient' => 'boolean',
    ];

    /**
     * Relasi ke Header KRS
     */
    public function studyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    /**
     * Relasi ke User Login
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Program Studi
     * Gunakan camelCase agar $student->studyProgram jalan
     */
    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    /**
     * Relasi ke Dosen Wali (PA)
     */
    public function academicAdvisor(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'academic_advisor_id');
    }

    /**
     * Relasi ke Kurikulum (PENTING untuk KRS)
     */
    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    /**
     * Relasi ke Tagihan Keuangan
     */
    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class);
    }
}