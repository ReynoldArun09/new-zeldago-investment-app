<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = ['user_id', 'amount', 'payout_method', 'payout_details', 'status', 'admin_message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
