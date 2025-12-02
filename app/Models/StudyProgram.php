<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyProgram extends Model
{
    protected $fillable = ['faculty_id', 'code', 'name', 'degree', 'head_name', 'head_nip', 'total_credits'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }
}
