<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntryCheckingRequest;
use App\Traits\EntryCheckingTrait;
use Illuminate\Support\Facades\DB;

class EntryCheckingController extends Controller
{
    use EntryCheckingTrait;
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function create()
    {
        $statistics=json_encode($this->statisticsStartValues());
        return view('entryChecking' , compact('statistics'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreEntryCheckingRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreEntryCheckingRequest $request)
    {
        $data = $request->validated();
        return response()->json(
            DB::transaction(function () use ($data) {
                return $this->canPass($data);
            })
        );
    }
}
