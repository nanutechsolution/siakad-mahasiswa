<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Payment extends Model
{
    use HasUlids;

    protected $fillable = [
        'billing_id', 'amount_paid', 'payment_method', 
        'proof_path', 'payment_date', 'status', 
        'rejection_note', 'verified_by'
    ];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function billing() {
        return $this->belongsTo(Billing::class);
    }

    public function verifier() {
        return $this->belongsTo(User::class, 'verified_by');
    }
}