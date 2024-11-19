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
        if ($user instanceof Academic) {
            $user->load([
                'couponOwner',
                'cardApplicant',
                'cardApplicant.currentCardApplication.cardLastUpdate' //:status
            ]);
            if (!$user->cardApplicant->currentCardApplication) // we have define the new cardApplication to receive the expiration of the  valid application minimum
                $user->load(
                    'cardApplicant.validCardApplication',//:expiration_date
                );
        }
        else if ($user instanceof CardApplicationStaff)
            $user->statistics;
        return response()->json([
            'message' => 'Login Successful',
            'user' => $user,
            'model' => class_basename($user),
        ]);

    }
}
