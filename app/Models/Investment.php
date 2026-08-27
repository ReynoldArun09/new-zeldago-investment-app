<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_CLOSED = 'CLOSED';
    public const STATUS_REJECTED = 'REJECTED';
    public const STATUS_CLOSE_REQUEST = 'CLOSE_REQUEST';

    protected $fillable = [
        'user_id',
        'trx_id',
        'amount',
        'contribution_amount',
        'contribution_frequency',
        'total_return',
        'type',
        'status',
        'payment_proof',
        'roi_cycle_start_date',
        'next_roi_date',
        'duration_months',
        'is_old',
    ];

    protected $casts = [
        'roi_cycle_start_date' => 'datetime',
        'next_roi_date' => 'datetime',
        'amount' => 'decimal:2',
        'contribution_amount' => 'decimal:2',
        'total_return' => 'decimal:2',
        'is_old' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function roiLogs()
    {
        return $this->hasMany(RoiLog::class, 'investment_id');
    }
}
