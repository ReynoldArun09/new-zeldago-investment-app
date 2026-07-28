<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = ['level_count', 'commissions'];

    protected $casts = [
        'commissions' => 'array',
    ];
}
