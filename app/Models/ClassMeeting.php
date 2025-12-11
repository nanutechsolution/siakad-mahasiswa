<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ClassMeeting extends Model
{
    use HasUlids;

    protected $fillable = ['classroom_id', 'meeting_no', 'meeting_date', 'topic', 'is_open', 'token'];
    protected $casts = ['meeting_date' => 'date', 'is_open' => 'boolean'];

    public function classroom() { return $this->belongsTo(Classroom::class); }
    public function attendances() { return $this->hasMany(Attendance::class); }
}