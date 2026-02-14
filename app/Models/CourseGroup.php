<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseGroup extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($model) {
            // Generate ULID otomatis jika kosong
            if (empty($model->id)) {
                $model->id = (string) Str::ulid();
            }
            // Paksa Kode jadi Huruf Besar (Mkk -> MKK)
            if (!empty($model->code)) {
                $model->code = strtoupper($model->code);
            }
        });

        static::updating(function ($model) {
            if (!empty($model->code)) {
                $model->code = strtoupper($model->code);
            }
        });
    }
}
