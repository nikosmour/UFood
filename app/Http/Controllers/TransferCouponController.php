<?php

namespace App\Http\Controllers;

use App\Enum\UserAbilityEnum;
use App\Http\Requests\StoreTransferCouponRequest;
use App\Models\Academic;
use App\Models\TransferCoupon;
use App\Traits\CouponOwnerTrait;
use DB;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class TransferCouponController extends Controller
{
    use CouponOwnerTrait;

    public function __construct()
    {
        $this->middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs');
        $this->middleware('ability:' . UserAbilityEnum::COUPON_OWNERSHIP->name);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTransferCouponRequest $request
     * @return Application|JsonResponse|RedirectResponse|Redirector
     */
    public function store(StoreTransferCouponRequest $request)
    {
        $validatedData = $request->validated();
        $transaction = DB::transaction(function () use ($validatedData) {
            return TransferCoupon::create($validatedData);
        });

        return $request->expectsJson()
            ? response()->json([
                "name" => Academic::whereAcademicId($validatedData['receiver_id'])->value('name'),
                'transaction' => "T$transaction->id"
            ])
            : redirect(route('coupons.transfer.create'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function create(): \Illuminate\Contracts\View\View|Factory|View|Application
    {
        $couponOwner = auth()->user()->couponOwner;
        return view('couponOwner.send', compact('couponOwner'));
    }
}
