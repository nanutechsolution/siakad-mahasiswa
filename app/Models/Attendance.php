<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Attendance extends Model
{
    use HasUlids;

    protected $fillable = ['class_meeting_id', 'student_id', 'status', 'check_in_at'];
    protected $casts = ['check_in_at' => 'datetime'];

    public function student() { return $this->belongsTo(Student::class); }
     public function class_meeting() { return $this->belongsTo(ClassMeeting::class); }
}