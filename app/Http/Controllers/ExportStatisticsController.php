<?php

namespace App\Http\Controllers;

use App\Enum\MealPlanPeriodEnum;
use App\Http\Requests\CreateExportStatisticsRequest;

class ExportStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs');
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(CreateExportStatisticsRequest $request)
    {
        $vData = $request->has('current') ? ['meal_category' => [MealPlanPeriodEnum::getCurrentMealPeriod()->value],
            'from_date' => now()->toDateString(),
            'to_date' => now()->toDateString(),
        ] : $request->validated();
        $caption = 'Statistics';
        $models = auth()->user()->takeStatistics($vData)->get();
        return view('PDFS.statistics', compact('models', 'caption'));
    }
}
