<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdomQuestion extends Model
{
    protected $fillable = [
        'category',
        'question_text',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function edom_responses()
    {
        return $this->hasMany(EdomResponse::class);
    }
}