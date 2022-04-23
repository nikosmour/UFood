<?php

namespace App\Http\Controllers;

use App\Models\MealPlan;
use Illuminate\Http\Request;

class DailyMealPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function __invoke()
    {
        $relation1s=['meal'];
        $models=MealPlan::with($relation1s)->where('date','>=',now()->toDateString() )->get();
        foreach ($relation1s as $relation1)
            $relations[]=explode('.',$relation1);

        return view('test',compact('models','relations'));

    }
}
