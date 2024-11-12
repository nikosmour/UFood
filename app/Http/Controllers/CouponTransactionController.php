<?php

namespace App\Http\Controllers;

use App\Enum\MealPlanPeriodEnum;
use App\Enum\UserAbilityEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CouponTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs');
        $this->middleware('ability:' . UserAbilityEnum::COUPON_OWNERSHIP->name);

    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function __invoke(Request $request)
    {
        $meals = MealPlanPeriodEnum::names();
        $couponOwner = Auth::user()->couponOwner;
        $mealColumnsUsing = collect($meals)->map(function ($meal) {
            return "CASE WHEN period = '$meal' THEN 1 ELSE 0 END as $meal";
        })->join(', ');
        $mealColumnsSending = collect($meals)->map(function ($meal) {
            return "CAST($meal AS SIGNED) * -1 as $meal";
        })->join(', ');
        $sending = $couponOwner->sendingCoupon()->select('id', DB::raw('"sending" as transaction, receiver_id as other_person_id,0 as money'), 'created_at', DB::raw($mealColumnsSending));
        $receiving = $couponOwner->receivingCoupon()->select('id', DB::raw(' "receiving" as transaction,sender_id as other_person_id,0 as money'), 'created_at', ...$meals);
        $buying = $couponOwner->purchaseCoupon()->select('id', DB::raw(' "buying" as transaction,0 as other_person_id,money/100 as money'), 'created_at', ...$meals);
        $using = $couponOwner->usageCoupon()->select('id', DB::raw('"using" as transaction,0 as other_person_id,0 as money'), 'created_at', DB::raw($mealColumnsUsing));
        $transactions = $sending->union($receiving)->union($buying)->union($using)->orderByDesc('created_at')->simplePaginate(10);
        return response()->json(["transactions" => $transactions]);

    }
}
