<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StudyProgram extends Model
{
    use SoftDeletes;

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

    protected $casts = [
        'is_package' => 'boolean',
    ];
    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }


    public function tuition_rates()
    {
        return $this->hasMany(TuitionRate::class);
    }
}
