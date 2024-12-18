<?php


namespace App\Traits;

use App\Enum\MealPlanPeriodEnum;
use App\Models\CouponOwner;

trait CouponOwnerTrait
{
    /**
     * @param int $couponOwner_id
     * @param array $coupons
     * @return void
     */
    static public function addCoupons(int $couponOwner_id, array $coupons): void
    {
        $couponOwner = CouponOwner::lockForUpdate()->find($couponOwner_id);
        foreach (MealPlanPeriodEnum::names() as $meal)
            $couponOwner[$meal] += $coupons[$meal] ?? 0;
        $couponOwner->save();
    }

    /**
     * @param int $couponOwner_id
     * @param array $coupons
     * @return void
     */
    static public function removeCoupons(int $couponOwner_id, array $coupons): void
    {
        $couponOwner = CouponOwner::lockForUpdate()->find($couponOwner_id);
        foreach (MealPlanPeriodEnum::names() as $meal)
            $couponOwner[$meal] -= $coupons[$meal] ?? 0;
        $couponOwner->save();
    }
}
