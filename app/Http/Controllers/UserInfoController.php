<?php

namespace App\Http\Controllers;

use App\Models\Academic;
use App\Models\CardApplicationStaff;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

class UserInfoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:academics,entryStaffs,couponStaffs,cardApplicationStaffs');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Authenticatable|null $user
     * @return JsonResponse
     */
    public function __invoke(?Authenticatable $user): JsonResponse
    {
        $user = ($user) ?: auth()->user();
        if ($user instanceof Academic)
            $user->load([
                'couponOwner',
                'cardApplicant',
                'cardApplicant.currentCardApplication',//:expiration_date
                'cardApplicant.currentCardApplication.cardLastUpdate' //:status
            ]);
        else if ($user instanceof CardApplicationStaff)
            $user->statistics;
        return response()->json([
            'message' => 'Login Successful',
            'user' => $user,
        ]);

    }
}
