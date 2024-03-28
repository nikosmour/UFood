<?php

namespace App\Http\Controllers;

use App\Enum\CardStatusEnum;
use App\Enum\UserAbilityEnum;
use App\Http\Requests\StoreCardApplicationCheckingRequest;
use App\Http\Requests\UpdateCardApplicationCheckingRequest;
use App\Models\CardApplication;
use App\Models\CardApplicationChecking;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CardApplicationCheckingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:cardApplicationStaffs');
        $this->middleware('ability:' . UserAbilityEnum::CARD_APPLICATION_CHECK->name);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(CardStatusEnum $category)
    {
        $query = \App\Models\CardApplicationUpdate::groupBy('card_application_id')->selectRaw('max(id) as max_id ');
        $models = $cardApplications = \App\Models\CardApplicationUpdate::whereStatus($category
        )->joinSub($query, 'mostResent', 'id', 'max_id')->select('card_application_id as id')->get();
        return view('cardApplicationChecking.index', compact('models', 'category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCardApplicationCheckingRequest $request
     * @return true
     */
    public function store(StoreCardApplicationCheckingRequest $request)
    {
        $vData = $request->validated();
        DB::transaction(function () use ($vData) {
            $data = isset($vData['card_application_staff_comment']) ? [
                'comment' => $vData['card_application_staff_comment'],
                'status' => $vData['status']
            ] : ['status' => $vData['status']];
            Auth::user()->cardApplication()->attach($vData['card_application_id'], $data);
            if (isset($vData['expiration_date']))
                CardApplication::whereId($vData['card_application_id'])->update(['expiration_date' => $vData['expiration_date']]);
            else
                CardApplication::whereId($vData['card_application_id'])->touch();
        });
        return true;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCardApplicationCheckingRequest $request
     * @param CardApplicationChecking $cardApplicationChecking
     * @return Response
     */
    public function update(UpdateCardApplicationCheckingRequest $request, CardApplicationChecking $cardApplicationChecking)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param CardApplicationChecking $cardApplicationChecking
     * @return Response
     */
    public function show(CardApplicationChecking $cardApplicationChecking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CardApplicationChecking $cardApplicationChecking
     * @return Response
     */
    public function edit(CardApplicationChecking $cardApplicationChecking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CardApplicationChecking $cardApplicationChecking
     * @return Response
     */
    public function destroy(CardApplicationChecking $cardApplicationChecking)
    {
        //
    }
}
