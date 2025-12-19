<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TuitionRate extends Model
{
    // Ganti fee_type jadi fee_type_id
    protected $fillable = ['study_program_id', 'fee_type_id', 'entry_year', 'amount', 'is_active'];

    public function study_program()
    {
        return $this->belongsTo(StudyProgram::class);
    }

    // Tambahkan Relasi ke Jenis Biaya
    public function fee_type()
    {
        return $this->belongsTo(FeeType::class);
    }
}