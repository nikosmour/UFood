<?php

namespace App\Http\Controllers;

use App\Enum\UserAbilityEnum;
use App\Http\Requests\StoreCardApplicationRequest;
use App\Http\Requests\UpdateCardApplicationRequest;
use App\Models\CardApplication;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class CardApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:academics');
        $this->middleware('ability:' . UserAbilityEnum::CARD_OWNERSHIP->name);

    }


    /**
     * @return Application|Factory|View|RedirectResponse|Redirector
     */
    public function index()
    {
        $user = Auth::user();
        if($user->cardApplicant->currentCardApplication()->count() > 0)
            return redirect(route('cardApplication.show', [
                "cardApplication"=>$user->cardApplicant->currentCardApplication
            ]));
        $user->cardApplicant->address;
        $models = [$user];
        $caption = 'User info';
        return view('cardApplication/index', compact('models', 'caption'));
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
     * @param StoreCardApplicationRequest $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(StoreCardApplicationRequest $request)
    {
        $cardApplication= new CardApplication();
        $cardApplication->academic_id= Auth::user()->cardApplicant->academic_id;
        $cardApplication->expiration_date= date('Y-m-d', strtotime('-1 day'));
        $cardApplication->save();
        return redirect(route('cardApplication.show', compact("cardApplication")));
    }

    /**
     * Display the specified resource.
     *
     * @param CardApplication $cardApplication
     * @return Response
     */
    public function show(CardApplication $cardApplication)
    {
        dd($cardApplication);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param CardApplication $cardApplication
     * @return Response
     */
    public function edit(CardApplication $cardApplication)
    {
        return view('cardApplication/edit', compact('cardApplication'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCardApplicationRequest $request
     * @param CardApplication $cardApplication
     * @return array
     */
    public function update(UpdateCardApplicationRequest $request, CardApplication $cardApplication)
    {

        if ($request->has('files'))
            $files = $request->file('files');
        else
            return ['success'=>false,'message'=>'there isn\'t files'];

        foreach ($files as $index => $file) {
            $filename = $file->getClientOriginalName();
            $file->storeAs('uploads/general/'."$cardApplication->academic_id", $filename);
        }
        return ['success'=>true,'message'=>'Files uploaded successfully!'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CardApplication $cardApplication
     * @return Response
     */
    public function destroy(CardApplication $cardApplication)
    {
        //
    }
}
