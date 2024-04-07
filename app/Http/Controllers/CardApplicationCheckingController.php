<?php

namespace App\Http\Controllers;

use App\Enum\CardStatusEnum;
use App\Enum\UserAbilityEnum;
use App\Events\CardApplicationUpdated;
use App\Http\Requests\StoreCardApplicationCheckingRequest;
use App\Http\Requests\UpdateCardApplicationCheckingRequest;
use App\Models\CardApplication;
use App\Models\CardApplicationChecking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
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
            $application = CardApplication::whereId($vData['card_application_id'])->with(['cardLastUpdate'])->first();
            $old_status = $application->cardLastUpdate->status;
            Auth::user()->cardApplication()->attach($vData['card_application_id'], $data);
            if (isset($vData['expiration_date'])) {
                $application->expiration_date = $vData['expiration_date'];
                $application->save();
            }
            else
                $application->touch();
            broadcast(event: new CardApplicationUpdated(
                cardApplication: $application,
                status: $vData['status'],
                old_status: $old_status,
                comment: $vData['card_application_staff_comment'] ?? null))->toOthers();
        });
        return true;
    }

    /**
     * @param Request $request
     * @return Builder[]|Collection
     */
    public function search(Request $request): Collection|array
    {
        $query = \App\Models\CardApplication::with(relations: ['cardLastUpdate', 'cardApplicationDocument']);
        if (isset($request['application_id']))
            $query->where('id', $request['application_id']);
        elseif (isset($request['academic_id']))
            $query->where('academic_id', $request['academic_id']);
        elseif (isset($request['a_m']))
            $query->whereRelation('academic', 'a_m', $request['a_m']);
        elseif (isset($request['email']))
            $query->whereRelation('academic', 'email', $request['email']);
        elseif (isset($request['status'])) {
            $query1 = \App\Models\CardApplicationUpdate::groupBy('card_application_id')->selectRaw('max(id) as max_id ');
            $query = \App\Models\CardApplicationUpdate::whereStatus($request['status']
            )->joinSub($query1, 'mostResent', 'id', 'max_id')->select('card_application_id as id');
        } else
            return [];
        return $query->get();
    }
}
