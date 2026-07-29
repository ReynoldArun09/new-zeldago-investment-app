<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'bank_name',
        'account_number',
        'ifsc_code',
        'upi_id',
        'upi_number',
        'proof_image',
        'status',
        'rejection_reason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
