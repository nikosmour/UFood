<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PhpOption\None;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    /**
     * Get the academic model associate with the user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne | null
     */
    public function academic()
    {
        return $this->hasOne(Academic::class);
    }
    public function cardApplicationStaff(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CardApplicationStaff::class);
    }
    public function couponStaff(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CouponStaff::class);
    }
    public function entryStaff(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(EntryStaff::class);
    }
}
