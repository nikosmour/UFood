<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponOwner extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $primaryKey = 'academic_id';

    public function getMoneyAttribute($money): float|int
    {
        return $money / 100;
    }

    public function setMoneyAttribute($money): float|int
    {
        $this->attributes['money'] = $money * 100;
    }
    public function academic(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Academic::class,'academic_id');
    }
    public function purchaseCoupon(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PurchaseCoupon::class, 'academic_id');
    }
    public function sendingCoupon(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransferCoupon::class,'sender_id');
    }
    public function receivingCoupon(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransferCoupon::class, 'receiver_id');
    }
    public function usageCoupon(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UsageCoupon::class, 'academic_id');
    }
}
