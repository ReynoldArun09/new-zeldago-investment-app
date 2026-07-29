<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Str;

#[Fillable(['sponsor_id', 'referral_code', 'account_type', 'name', 'username', 'email', 'phone', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function investments()
    {
        return $this->hasMany(Investment::class, 'user_id');
    }

    public function sponsor()
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    public function directReferrals()
    {
        return $this->hasMany(User::class, 'sponsor_id');
    }

    public function downline()
    {
        return $this->hasMany(User::class, 'sponsor_id')->with('downline');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function bankDetail()
    {
        return $this->hasOne(BankDetail::class);
    }

    public function kyc()
    {
        return $this->hasOne(Kyc::class);
    }

    public function nominee()
    {
        return $this->hasOne(Nominee::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->referral_code)) {
                do {
                    $code = strtoupper(Str::random(8));
                } while (self::where('referral_code', $code)->exists());
                $user->referral_code = $code;
            }
        });
    }
}
