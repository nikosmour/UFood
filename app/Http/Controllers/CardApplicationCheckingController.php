<?php

namespace App\Http\Controllers;

use App\Enum\CardStatusEnum;
use App\Enum\UserAbilityEnum;
use App\Events\CardApplicationUpdated;
use App\Http\Requests\SearchApplicationRequest;
use App\Http\Requests\StoreCardApplicationCheckingRequest;
use App\Models\CardApplication;
use App\Models\CardApplicationUpdate;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CardApplicationCheckingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs');
        $this->middleware('ability:' . UserAbilityEnum::CARD_APPLICATION_CHECK->name);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(CardStatusEnum $category)
    {
        $query = CardApplicationUpdate::groupBy('card_application_id')->selectRaw('max(id) as max_id ');
        $models = $cardApplications = CardApplicationUpdate::whereStatus($category
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
        $application = CardApplication::whereId($vData['card_application_id'])->with(['cardLastUpdate'])->first();
        // make validation comparing data in database
        $old_status = $application->cardLastUpdate['status']->value;
//        dd($old_status === CardStatusEnum::CHECKING->value);
//        dd($old_status,CardStatusEnum::CHECKING->value,$vData['status'], $vData['status'] === $old_status);

        $secondValidationRules = [
            'status' => [($old_status === CardStatusEnum::CHECKING->value) ?
                Rule::notIn([CardStatusEnum::CHECKING->value]) :
                Rule::in([CardStatusEnum::CHECKING->value])

            ],
            'expiration_date' => ['date', 'after_or_equal:' . $application['expiration_date']],

        ];
        Validator::make($vData, $secondValidationRules)->validate();
        $vData['status'] = CardStatusEnum::from($vData['status']);
//        dd('eutheuet');
        DB::transaction(function () use ($vData, $application) {
            $data = isset($vData['card_application_staff_comment']) ? [
                'comment' => $vData['card_application_staff_comment'],
                'status' => $vData['status']
            ] : ['status' => $vData['status']];

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
     * @param SearchApplicationRequest $request
     * @return array|CursorPaginator|Builder[]|Collection
     */
    public function search(Request $request)
    {
        $query = CardApplication::with(relations: ['cardLastUpdate', 'cardApplicationDocument', 'cardApplicant', 'academic', 'addresses']);
        if (isset($request['application_id']))
            $query->where('id', $request['application_id']);
        elseif (isset($request['academic_id']))
            $query->where('academic_id', $request['academic_id']);
        elseif (isset($request['a_m']))
            $query->whereRelation('academic', 'a_m', $request['a_m']);
        elseif (isset($request['email']))
            $query->whereRelation('academic', 'email', $request['email']);
        elseif (isset($request['status'])) {
            $query->whereRelation('cardLastUpdate', 'status', $request['status']);
            return $query->cursorPaginate(1);
        } else
            return [];
        return response()->json(['data' => $query->get()]);//->keyBy('id');
    }
}
