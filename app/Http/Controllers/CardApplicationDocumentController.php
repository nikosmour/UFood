<?php

namespace App\Http\Controllers;

use App\Enum\CardDocumentStatusEnum;
use App\Http\Requests\StoreCardApplicationDocumentRequest;
use App\Http\Requests\UpdateCardApplicationDocumentRequest;
use App\Models\CardApplication;
use App\Models\CardApplicationDocument;
use App\Traits\DocumentTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CardApplicationDocumentController extends Controller
{
    use DocumentTrait;

    public function __construct()
    {
        $this->middleware('auth:academics,staffs');
    }

    /**
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(CardApplication $cardApplication)
    {
        $this->authorize('viewAny', [CardApplicationDocument::class, $cardApplication]);
//        return  CardApplicationDocument::whereCardApplicationId($cardApplication->id)->select($select)->get();
        return response()->json([
            'documents' => $cardApplication->cardApplicationDocument()->get()
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(StoreCardApplicationDocumentRequest $request, CardApplication $cardApplication): JsonResponse
    {
        $this->authorize('create', [CardApplicationDocument::class, $cardApplication]);
        $id = DB::transaction(callback: function () use ($request, $cardApplication) {
            $file = $request->file('file');
            $description = $request['description'];
            $filename = $file->getClientOriginalName();
            $cardApplicationDocument = $cardApplication->cardApplicationDocument()->create(['file_name' => $filename, 'description' => $description, 'status' => CardDocumentStatusEnum::SUBMITTED]);
            $file->storeAs(...$this::storePositionData($cardApplication->academic_id, $cardApplicationDocument));
            return $cardApplicationDocument->id;
        });

        return response()->json(['success' => true, 'message' => 'file_submitted.success', 'id' => $id], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param CardApplicationDocument $document
     * @return Response
     * @throws AuthorizationException
     */
    public function show(CardApplicationDocument $document): Response
    {
        $this->authorize('view', $document);
        $cardApplication = $document->cardApplication;
        $fileStorageData = $this::storePositionData($cardApplication->academic_id, $document); // Adjust the file path according to your file storage location
        $filePath = $fileStorageData[0] . '/' . $fileStorageData[1];
        $disk = $fileStorageData[2];
        if ((config('app.evaluation') || !app()->environment('production')) and str_starts_with($document->file_name, '_fake_')) {
            $filePath = $document->file_name = str_replace('_fake_', '', $document->file_name);
            if ($document->file_name === 'otherInformation') {
                $pdf = PDF::loadView('PDFS.fakePDF');

                // Generate and output the PDF
                return $pdf->stream('_fake_otherInformation');
            }
            $disk = 'fake_doc';
            $filePath = ($document->file_name === 'AcademicId') ? $filePath . '_' . $document->cardApplication->Academic->status->name : $filePath;
            $filePath = $filePath . '.pdf';
        }
        // Check if the file exists
        if (Storage::disk($disk)->exists($filePath)) {
            // Retrieve the file's contents
            $fileContents = Storage::disk($disk)->get($filePath);

            // Determine the file's MIME type
            $mimeType = Storage::disk($disk)->mimeType($filePath);

            // Return the file response
            return response($fileContents)->header('Content-Type', $mimeType)->header('Content-Disposition', "inline; filename=$document->file_name");
        }

        // If the file doesn't exist, return a 404 response or handle it as per your requirements
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCardApplicationDocumentRequest $request
     * @param CardApplicationDocument $document
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateCardApplicationDocumentRequest $request, CardApplicationDocument $document): JsonResponse
    {
        $this->authorize('update', $document);
        if ($document->update($request->validated()))
            return response()->json(['success' => true, 'message' => 'file_updated.success', 'id' => $document->id], 200);
        return response()->json(['error' => 'Failed to update card application document.'], 500);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CardApplicationDocument $document
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function destroy(CardApplicationDocument $document): JsonResponse
    {
        $this->authorize('delete', $document);
        return !$document->delete() ?
            response()->json(['error' => 'Failed to delete card application document.'], 500) :
            response()->json(['success' => true, 'message' => 'file_destroyed.success', 'id' => 0], 200);

    }
}
