<?php

namespace App\Http\Controllers;

use App\Enum\MealPlanPeriodEnum;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CardHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:all,App\Models\CardApplicant']);
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function __invoke(Request $request): Application|Factory|\Illuminate\Contracts\View\View|View
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $cardApplicant = Auth::user()->academic->cardApplicant;
//        $transactions = $cardApplicant->usageCard()->get();
        return view('cardApplicant.index', compact('cardApplicant', ));

    }
}
