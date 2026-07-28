<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoiLog extends Model
{
    protected $fillable = [
        'trx_id',
        'investment_id',
        'user_id',
        'amount',
        'rate',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }
}
