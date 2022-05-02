<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferCouponRequest;
use App\Models\TransferCoupon;
use App\Traits\CouponOwnerTrait;
use Illuminate\Http\Response;

class TransferCouponController extends Controller
{
    use CouponOwnerTrait;
    public function __construct()
    {
        $this->middleware(['auth', 'can:create,App\Models\TransferCoupon']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $couponOwner = auth()->user()->academic->couponOwner;
        return view('couponOwner.send', compact('couponOwner'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTransferCouponRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(StoreTransferCouponRequest $request): \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    {
        $validatedData = $request->validated();
        \DB::transaction(function () use ($validatedData) {
            TransferCoupon::create($validatedData);
        });
        return redirect(route('coupons.transfer.create'));
    }

    /**
     * Display the specified resource.
     *
     * @param TransferCoupon $transferCoupon
     * @return Response
     */
    public function show(TransferCoupon $transferCoupon)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param TransferCoupon $transferCoupon
     * @return Response
     */
    public function destroy(TransferCoupon $transferCoupon)
    {
        //
    }
}
