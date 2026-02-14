<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AcademicPeriod extends Model
{
    // Konfigurasi ULID
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Kolom yang dapat diisi secara massal
     */
    protected $fillable = [
        'code',
        'name',
        'start_date',
        'end_date',
        'is_active',
        'allow_krs',
        'allow_input_score',
    ];

    /**
     * Konversi tipe data otomatis
     */
    protected $casts = [
        'is_active' => 'boolean',
        'allow_krs' => 'boolean',
        'allow_input_score' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Boot logic untuk generate ULID otomatis
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
        });
    }

    /**
     * Relasi ke Header KRS
     */
    public function study_plans(): HasMany
    {
        return $this->hasMany(StudyPlan::class);
    }

    /**
     * Relasi ke Kelas Perkuliahan (CourseClass)
     */
    public function courseClasses(): HasMany
    {
        return $this->hasMany(CourseClass::class);
    }
}   