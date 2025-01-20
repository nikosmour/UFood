<?php

namespace App\Traits;

use App\Enum\MealPlanPeriodEnum;
use App\Models\CardApplication;
use App\Models\CouponOwner;
use App\Models\PurchaseCoupon;
use App\Models\UsageCard;
use App\Models\UsageCoupon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use JetBrains\PhpStorm\ArrayShape;

trait EntryCheckingTrait
{
    /**
     * Check if the user can pass the entry point
     * @param array $data
     * @return JsonResponse
     * <p>
     * A string that define how the user pass the entry point or if didn't poss
     * </p>
     */
    private function canPass(array $data): JsonResponse
    {
        $status = 422;
        try {
            $this->canPassAsCardApplicant($data);
            return response()->json(['passWith' => 'card'], 200);
        } catch (ModelNotFoundException) {
            $cardErrorMessage = __('validation.card_expired_or_not_exist');
        } catch (QueryException $e) {
            if ($e->getCode() != 23000)
                throw $e;
            $cardErrorMessage = __('validation.card_had_used');
            $status = 409;
        }
        if (($data['only_active'] ?? false)) {
            unset($data['only_active']);
            $this->canPassAsCouponOwnerFree($data);
        }
        try {
            $this->canPassAsCouponOwner($data);
            return response()->json(['passWith' => 'coupon'], 200);
        } catch (ModelNotFoundException) {
            $couponErrorMessage = __('validation.exist', ['attribute' => 'coupon_owner']);
        } catch (QueryException $e) {
            if ($e->getCode() != 22003)
                throw $e;
            $couponErrorMessage = __('validation.coupon_insufficient');
            $status = 409;
        }
        return response()->json([
            'errors' => [
                'academic_id' => [
                    $cardErrorMessage . ' ' . $couponErrorMessage,
                ]
            ]
        ], $status);
    }

    /**
     * Check if the user can pass the entry point as couponOwner
     * @param array $data
     * @return bool
     * @throws ModelNotFoundException<Model>
     * <p>
     * A boolean that define if the user pass
     * </p>
     */
    private function canPassAsCardApplicant(array $data): bool
    {

        CardApplication::whereAcademicId($data['academic_id'])->where('expiration_date', '>=', now()->toDateString())->firstOrFail();
        UsageCard::create($data);
        return true;
    }

    /**
     * Check if the user can pass the entry point as couponOwner
     * @param array $data
     * @return bool
     * @throws ModelNotFoundException<Model>
     * <p>
     * A string that define how the user pass the entry point or if didn't poss
     * </p>
     */
    private function canPassAsCouponOwner(array $data): bool
    {
        CouponOwner::findOrFail($data['academic_id']);
        UsageCoupon::create($data);
        return true;
    }

    /**
     * Check prepare for the user to pass  for free;
     * @param array $data
     * @return bool
     * @throws ModelNotFoundException<Model>
     * <p>
     * A string that define how the user pass the entry point or if didn't poss
     * </p>
     */
    private function canPassAsCouponOwnerFree(array $data): bool
    {
        $purchase = PurchaseCoupon::make($data);
        $purchase->academic_id = $data['academic_id'];
        $purchase[MealPlanPeriodEnum::getCurrentMealPeriod()->name] = 1;
        $purchase->coupon_staff_id = 2;
        return $purchase->save();
    }

    /**
     * the already entries for current meal
     * @return array
     */
    #[ArrayShape(['coupons' => "int", 'cards' => "int"])]
    private function statisticsStartValues(): array
    {
        $currentMeal = MealPlanPeriodEnum::getCurrentMealPeriod()->value;
        $currentDate = now()->format('Y-m-d');
        return [
            'coupons' => UsageCoupon::all()->where('created_at', '>', $currentDate)->where('period', $currentMeal)->count(),
            'cards' => UsageCard::all()->where('date', '>', $currentDate)->where('period', $currentMeal)->count()
        ];
    }

}
