<?php

namespace App\Http\Controllers;

use App\Enum\UserAbilityEnum;
use App\Models\Academic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CouponTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:academics,staffs');
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
        /** @var Academic $academic */
        $academic = Auth::user();
        $transactions = $academic->couponTransactions()->simplePaginate(10);
        return response()->json(["transactions" => $transactions]);

    }
}
