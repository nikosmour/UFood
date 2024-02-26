<?php

namespace App\Models;

/**
 * @mixin IdeHelperCouponStaff
 */
class CouponStaff extends User
{
    public function purchaseCoupon(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PurchaseCoupon::class);
    }
}
