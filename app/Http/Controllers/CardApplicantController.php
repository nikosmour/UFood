<?php

namespace App\Http\Controllers;

use App\Enum\UserAbilityEnum;
use App\Http\Requests\StoreCardApplicantRequest;
use App\Http\Requests\UpdateCardApplicantRequest;
use Illuminate\Support\Facades\DB;

class CardApplicantController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs');
        $this->middleware('ability:' . UserAbilityEnum::CARD_OWNERSHIP->name);
    }

    public function __invoke(StoreCardApplicantRequest $request)
    {
// Perform the update or create without relationships
        DB::transaction(function () use ($request) {
            $vData = $request->validated();
            $addresses = $vData['addresses'];
            unset($vData['addresses']);
            $academic = auth()->user();
            $cardApplicant = $academic->cardApplicant()->setEagerLoads([])->updateOrCreate([], $vData);
            foreach ($addresses as $address) {
                $cardApplicant->addresses()->updateOrCreate(
                    [
                        'is_permanent' => $address['is_permanent']
                    ],
                    $address
                );
            }
//            (new CardApplicationController())->store();
        });
// Print or log the queries
        return response()->json(['message' => 'Card applicant and addresses updated successfully.'], 201);

    }
}
