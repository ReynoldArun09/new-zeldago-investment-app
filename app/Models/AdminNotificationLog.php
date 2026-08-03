<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminNotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'target',
        'target_user_id',
        'title',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
