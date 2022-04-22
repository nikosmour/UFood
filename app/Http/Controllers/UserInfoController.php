<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        Auth::setUser(\App\Models\User::all()->random());
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
