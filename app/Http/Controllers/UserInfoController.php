<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function __invoke()
    {
        $user=Auth::user();
        $academic=$user->academic;
        $relations=null;
        if ($academic)
            if($academic->cardApplicant)
                $relations=[['academic','cardApplicant']];
            else
                $relations=[['academic']];
        $models=[$user];
        return view('test',compact('models','relations'));

    }
}
