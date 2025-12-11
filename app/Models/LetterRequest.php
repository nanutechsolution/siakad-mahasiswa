<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class LetterRequest extends Model
{
    use HasUlids;

    protected $guarded = [];

    public function student() {
        return $this->belongsTo(Student::class);
    }
}