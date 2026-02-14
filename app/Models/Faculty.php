<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faculty extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];


    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
            if (!empty($model->code)) {
                $model->code = strtoupper($model->code);
            }
        });
    }
    public function study_programs()
    {
        return $this->hasMany(StudyProgram::class);
    }
}
