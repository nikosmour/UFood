<?php

namespace App\Models;

use App\Enum\UserAbilityEnum;
use App\Enum\UserStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property UserStatusEnum $status
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     * @var string[]
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => UserStatusEnum::class,
    ];

    /**
     * Get the academic model associate with the user
     * @return HasOne | null
     */
    public function academic(): ?HasOne
    {
        return $this->hasOne(Academic::class);
    }

    /**
     * Get the cardApplicationStaff model associate with the user
     * @return HasOne|null
     */
    public function cardApplicationStaff(): ?HasOne
    {
        return $this->hasOne(CardApplicationStaff::class);
    }

    /**
     * Get the entryStaff model associate with the user
     * @return HasOne|null
     */
    public function couponStaff(): ?HasOne
    {
        return $this->hasOne(CouponStaff::class);
    }

    /**
     * Get the entryStaff model associate with the user
     * @return HasOne|null
     */
    public function entryStaff(): ?HasOne
    {
        return $this->hasOne(EntryStaff::class);
    }

    /**
     * check if the instance has an ability
     * @param UserAbilityEnum $ability
     * @return bool
     */
    public function hasAbility(UserAbilityEnum $ability): bool
    {
        return $this->status->hasAbility($ability);
    }
    /**
     * check if the instance has an ability
     * @param UserAbilityEnum[] $abilities
     * @return bool
     */
    public function hasAnyAbility(array $abilities): bool
    {
        return $this->status->hasAnyAbility($abilities);
    }
}
