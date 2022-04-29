<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseCouponRequest;
use App\Traits\PurchaseCouponTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseCouponController extends Controller
{
    use PurchaseCouponTrait;

    public function __construct()
    {
        $this->middleware(['auth','can:create,App\Models\PurchaseCoupon']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View /Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function create()
    {
        $statistics = json_encode($this->statisticsStartValues());
        return view('purchaseCoupon', compact('statistics'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StorePurchaseCouponRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePurchaseCouponRequest $request)
    {
        $data = $request->validated();
        return response()->json(
            DB::transaction(function () use ($data) {
                return $this->canBuy($data);
            })
        );
    }
}
