<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseCoupon extends Model
{
    use HasFactory;
    public $guarded=[];
    public $timestamps = ['create_at'];//todo false if not work only one
    public function setUpdatedAt($value)
    {
        return;
    }
    public function couponOwner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CouponOwner::class,'academic_id');
    }
    public function couponStaff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CouponStaff::class);
    }
}
