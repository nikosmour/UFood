<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDailyMealPlanRequest;
use App\Http\Requests\UpdateDailyMealPlanRequest;
use App\Models\DailyMealPlan;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DailyMealPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
        $this->authorizeResource(DailyMealPlan::class, 'dailyMealPlan');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function index(): \Illuminate\Contracts\View\View|Factory|View|Application
    {
        $relation1s = ['breakfast', 'lunch', 'dinner'];
        $models = DailyMealPlan::all();
        foreach ($relation1s as $relation1)
            $relations[] = explode('.', $relation1);
        $caption='DailyMealPlan';
        return view('test', compact('models', 'relations','caption'));
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
     * @param StoreDailyMealPlanRequest $request
     * @return Response
     */
    public function store(StoreDailyMealPlanRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param DailyMealPlan $dailyMealPlan
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function show(DailyMealPlan $dailyMealPlan): \Illuminate\Contracts\View\View|Factory|View|Application
    {
        $relation1s = ['breakfast', 'lunch', 'dinner'];
        $models = [$dailyMealPlan];
        foreach ($relation1s as $relation1)
            $relations[] = explode('.', $relation1);

        return view('test', compact('models', 'relations'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param DailyMealPlan $dailyMealPlan
     * @return Response
     */
    public function edit(DailyMealPlan $dailyMealPlan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDailyMealPlanRequest $request
     * @param DailyMealPlan $dailyMealPlan
     * @return Response
     */
    public function update(UpdateDailyMealPlanRequest $request, DailyMealPlan $dailyMealPlan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param DailyMealPlan $dailyMealPlan
     * @return Response
     */
    public function destroy(DailyMealPlan $dailyMealPlan)
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
}
