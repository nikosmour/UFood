<?php

namespace App\Http\Controllers;

use App\Enum\UserAbilityEnum;
use App\Http\Requests\StoreCardApplicantRequest;
use App\Models\Academic;
use App\Models\CardApplicant;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CardApplicantController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth:academics,staffs');
        $this->middleware('ability:' . UserAbilityEnum::CARD_OWNERSHIP->name);
    }

    public function index(Request $request): JsonResponse
    {
        $request->validate(['byTheSystem' => 'in:true,false']);

        /** @var Academic $user */
        $user = auth()->user();
        /** @var CardApplicant|null $cardApplicant */
        $cardApplicant = !($request['byTheSystem'] === 'true')
            ? $user->cardApplicant()->withOnly(['addresses', 'departmentRelation'])->first()
            : $this->userService->getApplicantInfo($user);
        if ($cardApplicant) {
            return response()->json($cardApplicant->toArray());
        }
        return response()->json([
            'applicant' => [
                'department' => session('department'),
                'addresses' => []
            ]
        ], Response::HTTP_NOT_FOUND);

    }

    public function store(StoreCardApplicantRequest $request): JsonResponse
    {
        // Perform the update or create without relationships
        $vData = $request->validated();

        try {
            return DB::transaction(function () use ($vData) {
                $addresses = $vData['addresses'] ?? [];
                unset($vData['addresses']);
                $delPermanent = $vData["delPermanent"] ?? false;
                unset($vData['delPermanent']);

                /** @var Academic $academic */
                $academic = auth()->user();
                if (isset($vData['academic_id'])) {
                    $prev = $academic->academic_id;
                    $academic->academic_id = $vData['academic_id'];
                    $academic->save();
                    Auth::guard('academics')->login($academic);
                    Academic::on('secondary_mysql')->where('academic_id', $prev)->update(['academic_id' => $vData['academic_id']]);
                }

                // Update or create the card applicant without eager loads
                /** @var CardApplicant|null $cardApplicant */
                $cardApplicant = $academic->cardApplicant()->setEagerLoads([])->updateOrCreate([], $vData);

                if (!$cardApplicant) {
                    throw new Exception('Failed to update or create card applicant.');
                }

                // Handle addresses
                foreach ($addresses as $address) {
                    $updatedAddress = $cardApplicant->addresses()->updateOrCreate(
                        ['is_permanent' => $address['is_permanent']],
                        $address
                    );

                    if (!$updatedAddress) {
                        throw new Exception('Failed to update or create address.');
                    }
                }

                // Delete permanent address if requested
                if ($delPermanent) {
                    $deleted = $cardApplicant->addresses()->where('is_permanent', true)->delete();

                    if (!$deleted) {
                        throw new Exception('Failed to delete permanent address.');
                    }
                }

                // Call CardApplicationController's store method
                $cardApplicationResponse = (new CardApplicationController())->store();

                if ($cardApplicationResponse->status() !== 201) {
                    throw new Exception('Failed to create card application.');
                }

                return $cardApplicationResponse->setData([
                    "cardApplication" => $cardApplicationResponse->getData(true),
                    "CardApplicantUpdatedAt" => $cardApplicant->updated_at,
                ]);
            });
        } catch (Exception $e) {
            // Handle exceptions outside the transaction
            report($e); // Log the exception for debugging
            return response()->json([
                'message' => 'An error occurred during the operation.',
            ], 500);
        }
    }
}
