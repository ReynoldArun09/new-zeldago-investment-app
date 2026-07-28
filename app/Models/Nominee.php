<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    protected $fillable = ['user_id', 'name', 'relation', 'identity_front_proof', 'identity_back_proof', 'status', 'admin_message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
