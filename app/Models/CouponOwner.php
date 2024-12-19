<?php

namespace App\Models;

use App\Traits\CouponOwnerTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @mixin IdeHelperCouponOwner
 */
class CouponOwner extends Model
{
    use HasFactory, CouponOwnerTrait;

    public $incrementing = false;
    protected $primaryKey = 'academic_id';

    protected $hidden = ['created_at', 'updated_at'];

    public function getMoneyAttribute($money): float|int
    {
        return $money / 100;
    }

    public function setMoneyAttribute($money)
    {
        $this->attributes['money'] = $money * 100;
    }

    public function academic(): BelongsTo
    {
        return $this->belongsTo(Academic::class, 'academic_id');
    }

    public function purchaseCoupon(): HasMany
    {
        return $this->hasMany(PurchaseCoupon::class, 'academic_id');
    }

    public function sendingCoupon(): HasMany
    {
        return $this->hasMany(TransferCoupon::class, 'sender_id');
    }

    public function receivingCoupon(): HasMany
    {
        return $this->hasMany(TransferCoupon::class, 'receiver_id');
    }

    public function usageCoupon(): HasMany
    {
        return $this->hasMany(UsageCoupon::class, 'academic_id');
    }

    /**
     *  relationship for all the couponTransaction of the user
     * @return HasMany
     */
    public function couponTransactions(): HasMany
    {
        return $this->hasMany(CouponTransaction::class, 'academic_id');
    }

    public function couponTransactionLatest(): HasOne
    {
        return $this->hasOne(CouponTransaction::class, 'academic_id')->limit(1);
    }


}
