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
        foreach (MealPlanPeriodEnum::names() as $meal)
            if ($coupons[$meal])
                CouponOwner::where('academic_id', '=', $couponOwner_id)->increment($meal, $coupons[$meal]);
    }

    /**
     * @param int $couponOwner_id
     * @param array $coupons
     * @return void
     */
    static public function removeCoupons(int $couponOwner_id, array $coupons): void
    {
        foreach (MealPlanPeriodEnum::values() as $meal)
            if ($coupons[$meal])
                CouponOwner::where('academic_id', '=', $couponOwner_id)->decrement($meal, $coupons[$meal]);
    }
}
