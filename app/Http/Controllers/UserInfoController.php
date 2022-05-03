<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserInfoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function __invoke()
    {
        $user = Auth::user();
        $academic = $user->academic;
        $relations = null;
        if ($academic)
            if ($academic->cardApplicant)
                $relations = [['academic', 'cardApplicant']];
            else
                $relations = [['academic']];
        $models = [$user];
        $caption='User info';
        return view('test', compact('models', 'relations','caption'));

    }
}
