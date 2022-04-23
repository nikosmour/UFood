<?php


namespace App\Traits;

use App\Models\CouponOwner;

trait CouponOwnerTrait
{
    private function addCoupons(CouponOwner $couponOwner, Array $coupons)
    {
        foreach ($coupons as $key => $value)
            $couponOwner->increment($key, $value);
        $couponOwner->saveOrFail();
    }

    private function removeCoupons(CouponOwner $couponOwner, Array $coupons)
    {
        foreach ($coupons as $key => $value)
            $couponOwner->decrement($key, $value);
        $couponOwner->saveOrFail();
    }
}
