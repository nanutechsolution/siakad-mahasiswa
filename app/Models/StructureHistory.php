<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StructureHistory extends Model
{
    protected $guarded = [];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'is_active' => 'boolean'];

    public function structurable()
    {
        return $this->morphTo();
    }
}
