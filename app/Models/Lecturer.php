<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory, HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;

     protected $fillable = [
        'user_id',
        'study_program_id',
        'nidn',
        'nip_internal',
        'front_title',
        'back_title',
        'phone',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // --------------------------------------

    // (Opsional) Relasi ke Prodi Homebase jika nanti dibutuhkan
    public function study_program()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    // (Opsional) Relasi ke Kelas yang diajar
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function edom_responses()
    {
        return $this->hasManyThrough(EdomResponse::class, Classroom::class);
    }
}
