<?php

namespace App\Http\Controllers;

use App\Enum\CardDocumentStatusEnum;
use App\Http\Requests\StoreCardApplicationDocumentRequest;
use App\Http\Requests\UpdateCardApplicationDocumentRequest;
use App\Models\CardApplication;
use App\Models\CardApplicationDocument;
use App\Traits\DocumentTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CardApplicationDocumentController extends Controller
{
    use DocumentTrait;

    public function __construct()
    {
        $this->middleware('auth:academics,cardApplicationStaffs');
        //$this->middleware('ability:' . UserAbilityEnum::CARD_OWNERSHIP->name);

    }


    /**
     * @return CardApplicationDocument[]|Builder[]|Collection
     * @throws AuthorizationException
     */
    public function index($cardApplication)
    {
        $this->authorize('viewAny', CardApplicationDocument::class);
        return CardApplicationDocument::whereCardApplicationId($cardApplication)->select(['id', 'status'])->get();
    }

    /**
     * @throws Throwable
     */
    public function store(StoreCardApplicationDocumentRequest $request, CardApplication $cardApplication): array
    {
        $this->authorize('create', CardApplicationDocument::class);
//        if (Auth::user()->getAttribute('academic_id')!=$cardApplication->academic_id)
//            return ['success'=>false,
//                'message'=>'You don\'t have authority to update the Application ',
//            ];
        $id = DB::transaction(callback: function () use ($request, $cardApplication) {
            $file = $request->file('file');
            $description = $request['description'];
            if ($file->isValid() && $file->extension() == 'pdf') {
                $filename = $file->getClientOriginalName();
                $cardApplicationDocument = $cardApplication->cardApplicationDocument()->create(['file_name' => $filename, 'description' => $description, 'status' => CardDocumentStatusEnum::SUBMITTED]);
                $file->storeAs(...$this::storePositionData($cardApplication->academic_id, $cardApplicationDocument));
                return $cardApplicationDocument->id;
            } else  return 0; // the file is not valid
        });
        if (0 != $id) return ['success' => true, 'message' => 'File is uploaded successfully!', 'id' => $id,];
        return ['success' => false, 'message' => 'File is not valid please retry', 'id' => 0,];


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    /*public function create(CardApplication $cardApplication)
    {
        return ;

    }*/

    /**
     * Display the specified resource.
     *
     * @param CardApplication $cardApplication
     * @return Response
     * @throws AuthorizationException
     */
    public function show(CardApplication $cardApplication, CardApplicationDocument $document)
    {
        $this->authorize('view', $cardApplication);
//        if (Auth::user()->getAttribute('academic_id')!=$cardApplication->academic_id)
//            abort(403, 'Unauthorized Access');
        $fileStorageData = $this::storePositionData($cardApplication->academic_id, $document); // Adjust the file path according to your file storage location
        $filePath = $fileStorageData[0] . '/' . $fileStorageData[1];
        $disk = $fileStorageData[2];
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
     * Show the form for editing the specified resource.
     *
     * @param CardApplication $cardApplication
     * @return Response
     */
    /*public function edit(CardApplication $cardApplication)
    {
    }*/

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCardApplicationDocumentRequest $request
     * @param int $cardApplication
     * @param int $document
     * @return array
     */
    public function update(UpdateCardApplicationDocumentRequest $request, int $cardApplication, int $document)
    {
        if (CardApplicationDocument::whereId($document)->update($request->validated()))
            return ['success' => true, 'message' => 'File has updated successfully!', 'id' => $document];

        return ['success' => false, 'message' => 'File has not updated successfully retry', 'id' => 0,];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param CardApplication $cardApplication
     * @return Response
     */
    /*public function destroy(CardApplication $cardApplication)
    {
        //
    }*/
}
