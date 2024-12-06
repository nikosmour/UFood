<?php

namespace App\Http\Controllers;

use App\Enum\CardDocumentStatusEnum;
use App\Enum\CardStatusEnum;
use App\Http\Requests\UpdateCardApplicationRequest;
use App\Models\Academic;
use App\Models\CardApplicant;
use App\Models\CardApplication;
use App\Models\CardApplicationDocument;
use App\Models\CardApplicationStaff;
use App\Traits\DocumentTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class CardApplicationController extends Controller
{
    use DocumentTrait;

    public function __construct()
    {
        $this->middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs');
    }

    /**
     * @return Application|Factory|View|RedirectResponse|Redirector|JsonResponse
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', CardApplication::class);
        /** @var Academic $user */
        $user = Auth::user();
        $user->load('cardApplicant.currentCardApplication.cardLastUpdate');
        $currentCardApplication = $user->cardApplicant->currentCardApplication;
        if ($currentCardApplication) {
            $responseData = [
                "cardApplication" => $currentCardApplication
            ];
            return response()->json($responseData, 200);
        }

        return response()->json(["message" => 'Application not found. Please create a new one.'], 404);

//        $user->cardApplicant->address;
//        $models = [$user];
//        $caption = 'User info';
//        return view('cardApplication/index', compact('models', 'caption'));
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
     * @return JsonResponse
     */
    public function store()
    {
        $this->authorize('create', CardApplication::class);
        $cardApplication = DB::transaction(function () {
            /** @var CardApplicant $cardApplicant */
            $cardApplicant = Auth::user()->cardApplicant()->withOnly('validCardApplication:id,expiration_date,academic_id')->first();
            $last_expiration = $cardApplicant->validCardApplication?->expiration_date ?? now()->subDay();
            /** @var CardApplication $cardApplication */
            $cardApplication = $cardApplicant->currentCardApplication()->create([
                "expiration_date" => $last_expiration
            ]);
            $cardApplication->applicantComments()->create(['comment' => '']);
            $cardApplication->load('cardLastUpdate');
            return $cardApplication;
        });
        return response()->json($cardApplication, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param CardApplication $cardApplication
     * @return Response
     */
    public function show(CardApplication $cardApplication)
    {
        $this->authorize('view', $cardApplication);
        dd($cardApplication);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $cardApplication
     * @return JsonResponse
     */
    public function edit(int $cardApplicationID): JsonResponse
    {
        return DB::transaction(function () use ($cardApplicationID) {
            $user = auth()->user();
            $cardApplication = CardApplication::with('cardLastUpdate')
                ->where('id', $cardApplicationID)
                ->lockForUpdate()
                ->first();
            $this->authorize('update', $cardApplication);
            $lastUpdate = $cardApplication->cardLastUpdate;

            if ($user instanceof Academic)
                if ($lastUpdate && in_array($lastUpdate->status, [CardStatusEnum::SUBMITTED, CardStatusEnum::TEMPORARY_SAVED]))
                    $lastUpdate->status = CardStatusEnum::TEMPORARY_SAVED;
                else
                    $lastUpdate = $cardApplication->cardLastUpdate()->make(['status' => CardStatusEnum::TEMPORARY_SAVED]);
            elseif ($user instanceof CardApplicationStaff)
                if ($lastUpdate->card_application_staff_id === $user->id)
                    $lastUpdate->status = CardStatusEnum::CHECKING;
                else
                    $lastUpdate = $cardApplication->cardLastUpdate()->make(['status' => CardStatusEnum::CHECKING]);
            $lastUpdate->save();

            $cardApplication->touch();
            return response()->json(['card_last_update' => $lastUpdate]);
        });

    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCardApplicationRequest $request
     * @param CardApplication $cardApplication
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(UpdateCardApplicationRequest $request, CardApplication $cardApplication): JsonResponse
    {
        $this->authorize('update', $cardApplication);
        $vData = $request->validated();
        $vData['status'] = CardStatusEnum::from($vData['status']);


        $noUpdatedIds = DB::transaction(function () use ($vData, $cardApplication) {
            $noUpdatedIds = $this->handleDocumentUpdates($vData['documents']['update'] ?? [], $cardApplication);

            $noDeletedIds = $this->handleDocumentDeletions($vData['documents']['delete'] ?? [], $cardApplication);
            if (!empty($noDeletedIds)) {
                if ($noDeletedIds['unDeleted'])
                    $noUpdatedIds['unDeleted'] = $noDeletedIds['unDeleted'];
                if (isset($noDeletedIds['unFounded']))
                    $noUpdatedIds['unFounded'] = array_merge($noUpdatedIds['unFounded'] ?? [], $noDeletedIds['unFounded']);
            }
            return $noUpdatedIds;
        });

        // Check for incomplete documents before submitting
        if ($vData['status'] === CardStatusEnum::SUBMITTED && $this->hasIncompleteDocuments($cardApplication)) {
            return response()->json(['success' => false, 'message' => 'You have incomplete documents. Please update or delete them.', 'noUpdatedIds' => $noUpdatedIds], 422);
        }

        // Update the application status and handle comments within a transaction
        $this->updateApplicationStatus($vData, $cardApplication);

        return response()->json(['message' => 'Application has been updated'], 200);
    }

    /**
     * @throws AuthorizationException
     */
    private function handleDocumentUpdates(array $documentsToUpdate, CardApplication $cardApplication): array
    {
        if (empty($documentsToUpdate)) return [];
        $updates = array_column($documentsToUpdate, 'description', 'id');
        $documents = $cardApplication->cardApplicationDocument()->whereIn('id', array_keys($updates))->select('id', 'status')->get();
        $cantUpdateIds = [];
        if (count($documents) !== count($documentsToUpdate)) {
            $cantUpdateIds['unFounded'] = array_diff(array_keys($documentsToUpdate), $documents->pluck('id')->toArray());
        }
        $updatableDocuments = $documents->filter(function (CardApplicationDocument $document) {
            return $document->status->canBeUpdated();
        });
        if (count($documents) !== count($updatableDocuments)) {
            $cantUpdateIds['unUpdated'] = $documents->filter(function (CardApplicationDocument $document) {
                return !$document->status->canBeUpdated();
            })->pluck('id')->toArray();
        }

        // Update document descriptions and status
        $updatableDocuments->each(function (CardApplicationDocument $document) use ($updates) {
            $document->description = $updates[$document->id];
            $document->status = CardDocumentStatusEnum::SUBMITTED;
        });

        $cardApplication->cardApplicationDocument()->saveMany($updatableDocuments);
        return $cantUpdateIds;
    }

    private function handleDocumentDeletions(array $documentsToDelete, CardApplication $cardApplication): array
    {
        if (empty($documentsToDelete)) return [];
        $documents = $cardApplication->cardApplicationDocument()->whereIn('id', $documentsToDelete)->select('id', 'status')->get();
        $cantDeletedIds = [];
        if (count($documents) !== count($documentsToDelete)) {
            $cantDeletedIds['unFounded'] = array_diff($documentsToDelete, $documents->pluck('id')->toArray());
        }
        $deletableDocuments = $documents->filter(function (CardApplicationDocument $document) {
            return $document->status->canBeDeleted();
        });
        if (count($documents) !== count($deletableDocuments)) {
            $cantDeletedIds['unDeleted'] = $documents->filter(function (CardApplicationDocument $document) {
                return !$document->status->canBeDeleted();
            })->pluck('id')->toArray();
        }

        // Update document descriptions and status
        $deletableDocuments->each(function (CardApplicationDocument $document) {
            $document->delete();
        });
        return $cantDeletedIds;
    }

    private function hasIncompleteDocuments(CardApplication $cardApplication): bool
    {
        return $cardApplication->cardApplicationDocument()->where('status', CardDocumentStatusEnum::INCOMPLETE)->exists();
    }

    private function updateApplicationStatus(array $vData, CardApplication $cardApplication): void
    {
        DB::transaction(function () use ($vData, $cardApplication) {
            $oldStatus = $cardApplication->cardLastUpdate->status ?? null;

            if (!in_array($oldStatus, [CardStatusEnum::SUBMITTED, CardStatusEnum::TEMPORARY_SAVED])) {
                $cardApplication->applicantComments()->create(['comment' => $vData['comment'] ?? null, 'status' => $vData['status'],]);
            } else {
                $cardApplication->cardLastUpdate->comment = $vData['comment'] ?? $oldStatus;
                $cardApplication->cardLastUpdate->status = $vData['status'];
            }

            $cardApplication->push();
        });
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
