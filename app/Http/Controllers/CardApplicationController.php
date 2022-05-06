<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardApplicationRequest;
use App\Http\Requests\UpdateCardApplicationRequest;
use App\Models\CardApplication;

class CardApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:all,'.CardApplication::class ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCardApplicationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCardApplicationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CardApplication  $cardApplication
     * @return \Illuminate\Http\Response
     */
    public function show(CardApplication $cardApplication)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CardApplication  $cardApplication
     * @return \Illuminate\Http\Response
     */
    public function edit(CardApplication $cardApplication)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCardApplicationRequest  $request
     * @param  \App\Models\CardApplication  $cardApplication
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCardApplicationRequest $request, CardApplication $cardApplication)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CardApplication  $cardApplication
     * @return \Illuminate\Http\Response
     */
    public function destroy(CardApplication $cardApplication)
    {
        //
    }
}
