<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use App\Enums\RegistrantStatus;

class Registrant extends Model
{
    use HasUlids;

    protected $guarded = ['id'];

    protected $casts = [
        'documents' => 'array',
        'status' => RegistrantStatus::class, // Casting Enum
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function firstChoice() { return $this->belongsTo(StudyProgram::class, 'first_choice_id'); }
    public function secondChoice() { return $this->belongsTo(StudyProgram::class, 'second_choice_id'); }
}