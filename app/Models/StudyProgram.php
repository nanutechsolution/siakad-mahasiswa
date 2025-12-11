<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyProgram extends Model
{
    protected $fillable = ['faculty_id', 'code', 'name', 'degree', 'head_name', 'head_nip', 'total_credits', 'is_package'];

    protected $casts = [
        'is_package' => 'boolean', // Casting agar jadi true/false
    ];
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

     public function students()
    {
        return $this->hasMany(Student::class);
    }
}
