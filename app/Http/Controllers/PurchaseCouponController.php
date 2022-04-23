<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseCouponRequest;
use App\Models\CouponOwner;
use App\Models\PurchaseCoupon;
use App\Traits\CouponOwnerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseCouponController extends Controller
{
    use CouponOwnerTrait;
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function create()
    {
        return view('purchaseCoupon');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePurchaseCouponRequest  $request
     * @return String
     */
    public function store(StorePurchaseCouponRequest $request)
    {
        $data = $request->validated();
        DB::transaction(function () use ($data) {
            /** @noinspection PhpUndefinedFieldInspection */
            Auth::user()->couponStaff->purchaseCoupon()->save(new PurchaseCoupon($data));
            $couponOwner=CouponOwner::find($data['academic_id']);
            unset($data['academic_id']);
            $this->addCoupons($couponOwner, $data);
        });
        return  "Επιτυχής πώληση";
    }
}
