<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCardApplicationCheckingRequest;
use App\Http\Requests\UpdateCardApplicationCheckingRequest;
use App\Models\CardApplicationChecking;

class CardApplicationCheckingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:all,'.CardApplicationChecking::class]);
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
     * @param  \App\Http\Requests\StoreCardApplicationCheckingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCardApplicationCheckingRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CardApplicationChecking  $cardApplicationChecking
     * @return \Illuminate\Http\Response
     */
    public function show(CardApplicationChecking $cardApplicationChecking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CardApplicationChecking  $cardApplicationChecking
     * @return \Illuminate\Http\Response
     */
    public function edit(CardApplicationChecking $cardApplicationChecking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCardApplicationCheckingRequest  $request
     * @param  \App\Models\CardApplicationChecking  $cardApplicationChecking
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCardApplicationCheckingRequest $request, CardApplicationChecking $cardApplicationChecking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CardApplicationChecking  $cardApplicationChecking
     * @return \Illuminate\Http\Response
     */
    public function destroy(CardApplicationChecking $cardApplicationChecking)
    {
        //
    }
}
