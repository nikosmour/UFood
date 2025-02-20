<?php

namespace App\Http\Controllers;

use App\Enum\MealPlanPeriodEnum;
use App\Enum\UserAbilityEnum;
use App\Http\Requests\CreateExportStatisticsRequest;
use App\Models\CouponStaff;
use App\Models\EntryStaff;
use App\Models\Staff;

class ExportStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:academics,staffs');
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateExportStatisticsRequest $request)
    {
        /** @var Staff $user */
        $user = auth()->user();
        abort_if(!$user->hasAnyAbility([
            UserAbilityEnum::ENTRY_CHECK,
            UserAbilityEnum::COUPON_SELL
        ]), 403);
        $referer = parse_url($request->header('referer'), PHP_URL_PATH);
        $class = EntryStaff::class;
        if ($referer === '/purchase')
            $class = CouponStaff::class;
        $vData = $request->has('current') ? ['meal_category' => [MealPlanPeriodEnum::getCurrentMealPeriod()->value],
            'from_date' => now()->toDateString(),
            'to_date' => now()->toDateString(),
        ] : $request->validated();
        $models = (new $class())->takeStatistics($vData)->get();
        return $models;
    }
}
