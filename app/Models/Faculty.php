<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    protected $guarded = [];

    public function study_programs()
    {
        return $this->hasMany(StudyProgram::class);
    }
}
