<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    protected $fillable = [
        'user_id', 
        'document_type', 
        'document_number', 
        'document_front_proof', 
        'document_back_proof', 
        'country', 
        'address', 
        'status', 
        'admin_message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
