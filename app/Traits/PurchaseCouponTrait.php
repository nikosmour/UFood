<?php

namespace App\Traits;

use App\Enum\MealPlanPeriodEnum;
use App\Models\CouponOwner;
use App\Models\PurchaseCoupon;
use Illuminate\Support\Facades\Auth;
use JetBrains\PhpStorm\ArrayShape;

trait PurchaseCouponTrait
{
    use CouponOwnerTrait;

    /**
     * Check if the user can pass the entry point
     * @param array $data
     * @return array
     * <p>
     * A string that define how the user pass the entry point or if didn't poss
     * </p>
     */
    #[ArrayShape(['sold' => "bool"])]
    private function canBuy(array $data): array
    {
        /** @noinspection PhpUndefinedFieldInspection */
        Auth::user()->couponStaff->purchaseCoupon()->save(new PurchaseCoupon($data));
        $couponOwner = CouponOwner::find($data['academic_id']);
        unset($data['academic_id']);
        $this->addCoupons($couponOwner, $data);
        return ['sold' => true];
    }

    /**
     * the already payments for current day
     * @return array
     * <p>
     * ["each category of meals" => "int sum of the coupons"]
     * </p>
     */
    private function statisticsStartValues(): array
    {
        $currentDate = now()->format('Y-m-d');
        $array = [];
        foreach (MealPlanPeriodEnum::names() as $period)
            $array[$period] = PurchaseCoupon::all()->where('created_at', '>', $currentDate)->sum($period);
        return $array;
    }

}
