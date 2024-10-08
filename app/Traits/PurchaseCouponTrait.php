<?php

namespace App\Traits;

use App\Enum\MealPlanPeriodEnum;
use App\Models\Academic;
use App\Models\PurchaseCoupon;

trait PurchaseCouponTrait
{

    /**
     * Check if the user can pass the entry point
     * @param array $data
     * @return array
     * <p>
     * A string that define how the user pass the entry point or if didn't poss
     * </p>
     */
    //#[ArrayShape(['success' => "bool"])]
    private function canBuy(array $data): array
    {
        $transaction = PurchaseCoupon::create($data);
        return ['success' => true,
            'receiver' => Academic::whereAcademicId($data['academic_id'])->value('name'),
            'transaction' => "P$transaction->id"];
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
