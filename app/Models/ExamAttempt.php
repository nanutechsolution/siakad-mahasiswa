<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ExamAttempt extends Model
{
    use HasUlids;
    
    protected $guarded = [];
    
    protected $casts = [
        'answers' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function registrant() {
        return $this->belongsTo(Registrant::class);
    }
}