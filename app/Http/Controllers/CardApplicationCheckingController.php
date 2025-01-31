<?php

namespace App\Http\Controllers;

use App\Enum\CardStatusEnum;
use App\Enum\UserAbilityEnum;
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
        $this->authorize('update', $application);
        // make validation comparing data in database
//        $old_status = $application->cardLastUpdate['status'];
//        dd($old_status === CardStatusEnum::CHECKING->value);
//        dd($old_status,CardStatusEnum::CHECKING->value,$vData['status'], $vData['status'] === $old_status);
        $secondValidationRules = [
            'status' => Rule::in([
                    CardStatusEnum::TEMPORARY_CHECKED->value,
                    CardStatusEnum::ACCEPTED->value,
                    CardStatusEnum::REJECTED->value,
                    CardStatusEnum::INCOMPLETE->value,
                ]
            ),
            //
            //            ],
            'expiration_date' => ['date', 'after_or_equal:' . $application['expiration_date']],

        ];
        Validator::make($vData, $secondValidationRules)->validate();
        $vData['status'] = CardStatusEnum::from($vData['status']);
//        dd('eutheuet');
        DB::transaction(function () use ($vData, $application) {
            if (isset($vData['expiration_date'])) {
                $application->expiration_date = $vData['expiration_date'];
                $application->save();
                unset($vData['expiration_date']);
            }
            else
                $application->touch();
            $cardUpdate = $application->cardLastUpdate;
            $cardUpdate->comment = $vData['card_application_staff_comment'] ?? '';
            $cardUpdate->update($vData);
//            $data = isset($vData['card_application_staff_comment']) ? [
//                'comment' => $vData['card_application_staff_comment'],
//                'status' => $vData['status']
//            ] : ['status' => $vData['status']];
//
//            $old_status = $application->cardLastUpdate->status;
////            Auth::user()->cardApplication()->attach($vData['card_application_id'], $data);
//            CardApplicationUpdate::whereCardApplicationId($application->id)->update(['status' => $vData['status']]);
//            broadcast(event: new CardApplicationUpdated(
//                cardApplicationUpdate: $update))->toOthers();
        });
        return true;
    }

    /**
     * @param SearchApplicationRequest $request
     * @return array|CursorPaginator|Builder[]|Collection
     */
    public function search(Request $request)
    {
        $query = CardApplication::with(relations: [
            'cardLastUpdate',
            'cardApplicationDocument',
            'academic.cardApplicant.addresses',
        ]);
        if (isset($request['application_id']))
            $query->where('id', $request['application_id']);
        elseif (isset($request['academic_id']))
            $query->where('academic_id', $request['academic_id']);
        elseif (isset($request['a_m']))
            $query->whereRelation('academic', 'a_m', $request['a_m']);
        elseif (isset($request['email']))
            $query->whereRelation('academic', 'email', $request['email']);
        elseif (isset($request['status'])) {
            $query1 = CardApplicationUpdate::groupBy('card_application_id')->selectRaw('max(id) as max_id ');
            $query2 = CardApplicationUpdate::whereStatus($request['status']
            )->joinSub($query1, 'mostResent', 'id', 'max_id')->select('card_application_id as id');
            // Paginate the result
            return $query->whereIn('id', $query2)->cursorPaginate(1);
        } else
            return [];
        return response()->json(['data' => $query->get()]);//->keyBy('id');
    }
}
