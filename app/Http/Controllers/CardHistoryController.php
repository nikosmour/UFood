<?php

namespace App\Http\Controllers;

use App\Enum\UserAbilityEnum;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CardHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:academics,staffs');
        $this->middleware('ability:' . UserAbilityEnum::CARD_OWNERSHIP->name);
    }

    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function __invoke(Request $request): Application|Factory|\Illuminate\Contracts\View\View|View|JsonResponse
    {

//        $cardApplicant = Auth::user()->cardApplicant()->with('usageCard')->first();
        $transactions = Auth::user()->cardApplicant->usageCard()->select('date', 'time', 'period')->simplepaginate(10);

        return $request->expectsJson()
            ? response()->json(["transactions" => $transactions, 'success' => true])
            : view('cardApplicant.index', compact('transactions'));

    }
}
