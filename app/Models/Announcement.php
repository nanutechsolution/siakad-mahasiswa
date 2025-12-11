<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'attachment', 'target_role', 'is_active', 'created_by'];
    
    protected $casts = ['is_active' => 'boolean'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }
}