<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseCouponRequest;
use App\Http\Requests\UpdatePurchaseCouponRequest;
use App\Models\PurchaseCoupon;

class PurchaseCouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePurchaseCouponRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePurchaseCouponRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PurchaseCoupon  $purchaseCoupon
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseCoupon $purchaseCoupon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseCoupon  $purchaseCoupon
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseCoupon $purchaseCoupon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePurchaseCouponRequest  $request
     * @param  \App\Models\PurchaseCoupon  $purchaseCoupon
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePurchaseCouponRequest $request, PurchaseCoupon $purchaseCoupon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseCoupon  $purchaseCoupon
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseCoupon $purchaseCoupon)
    {
        //
    }
}
