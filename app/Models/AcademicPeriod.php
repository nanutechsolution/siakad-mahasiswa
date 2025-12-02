<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicPeriod extends Model
{
    // Tambahkan semua kolom ini agar bisa di-update via Settings
    protected $fillable = [
        'code',
        'name',
        'start_date',
        'end_date',
        'is_active',
        'allow_krs',
        'allow_input_score',
    ];

    // Casting agar tipe datanya otomatis jadi boolean (true/false) saat dipanggil
    protected $casts = [
        'is_active' => 'boolean',
        'allow_krs' => 'boolean',
        'allow_input_score' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function study_plans()
    {
        return $this->hasMany(StudyPlan::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }
}
