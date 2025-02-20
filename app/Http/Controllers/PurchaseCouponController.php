<?php

namespace App\Http\Controllers;

use App\Enum\UserAbilityEnum;
use App\Http\Requests\StorePurchaseCouponRequest;
use App\Traits\PurchaseCouponTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseCouponController extends Controller
{
    use PurchaseCouponTrait;

    public function __construct()
    {
        $this->middleware('auth:academics,staffs');
        $this->middleware('ability:' . UserAbilityEnum::COUPON_SELL->name);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return array|Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function create(Request $request)
    {
        if ($request->expectsJson()) return $this->statisticsStartValues();
        $statistics = json_encode($this->statisticsStartValues());
        return view('purchaseCoupon.create', compact('statistics'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePurchaseCouponRequest $request
     * @return JsonResponse
     */
    public function store(StorePurchaseCouponRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['academic_id'] = $data['receiver_id'];
        unset($data['receiver_id']);
        return response()->json(
            DB::transaction(function () use ($data) {
                return $this->canBuy($data);
            })
        );
    }
}
