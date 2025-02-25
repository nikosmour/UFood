<?php

namespace App\Http\Controllers;

use App\Enum\UserAbilityEnum;
use App\Http\Requests\StoreEntryCheckingRequest;
use App\Traits\EntryCheckingTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EntryCheckingController extends Controller
{
    use EntryCheckingTrait;

    public function __construct()
    {
        $this->middleware('auth:academics,staffs');
        $this->middleware('ability:' . UserAbilityEnum::ENTRY_CHECK->name);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return array|Application|Factory|\Illuminate\Contracts\View\View|View
     */
    public function create(Request $request)
    {
        if ($request->expectsJson()) return $this->statisticsStartValues();
        $statistics = json_encode($this->statisticsStartValues());
        return view('entryChecking.create', compact('statistics'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreEntryCheckingRequest $request
     * @return JsonResponse
     */
    public function store(StoreEntryCheckingRequest $request): JsonResponse
    {
        $data = $request->validated();
        return DB::transaction(function () use ($data) {
                return $this->canPass($data);
        }
        );
    }
}
