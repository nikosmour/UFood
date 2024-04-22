<?php

namespace App\Http\Controllers;

use App\Enum\MealPlanPeriodEnum;
use App\Http\Requests\CreateExportStatisticsRequest;
use App\Models\PurchaseCoupon;
use App\Models\UsageCard;
use App\Models\UsageCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $vData = $request->validated();
        $caption = 'Statistics';
        $models = auth()->user()->takeStatistics($vData)->get();
        return view('PDFS.statistics', compact('models', 'caption'));
    }
}
