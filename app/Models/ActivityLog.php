<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'action', 'subject_type', 'subject_id', 'description', 'ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Helper untuk mendapatkan nama model yang lebih manusiawi
    public function getSubjectNameAttribute()
    {
        if (!$this->subject_type) return '-';
        return class_basename($this->subject_type); // App\Models\Student -> Student
    }
}