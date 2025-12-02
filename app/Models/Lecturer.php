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
}
