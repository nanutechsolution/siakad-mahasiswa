<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ThesisLog extends Model
{
    use HasUlids;

    protected $fillable = [
        'thesis_id', 'guidance_date', 'notes', 
        'student_notes', 'file_attachment', 'status'
    ];

    protected $casts = [
        'guidance_date' => 'date',
    ];

    public function thesis()
    {
        return $this->belongsTo(Thesis::class);
    }
}