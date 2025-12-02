<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = ['classroom_id', 'day', 'start_time', 'end_time', 'room_name'];
    
    public function classroom() { return $this->belongsTo(Classroom::class); }
}