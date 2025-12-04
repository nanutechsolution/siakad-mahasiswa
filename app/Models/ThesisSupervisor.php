<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThesisSupervisor extends Model
{
    protected $fillable = ['thesis_id', 'lecturer_id', 'role', 'status'];

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }
}