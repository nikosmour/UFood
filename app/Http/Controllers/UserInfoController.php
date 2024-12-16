<?php

namespace App\Http\Controllers;

use App\Models\Academic;
use App\Models\CardApplicationStaff;
use Auth;
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
        $this->middleware('authWithTemporary:academics,entryStaffs,couponStaffs,cardApplicationStaffs');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Authenticatable|null $user
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        if (session()->has('temporary_user')) {
            $temp = session('temporary_user');
            return response()->json([
                'message' => 'Login with temporary User',
                'user' => $temp['user'],
                'model' => class_basename($temp['model']),
            ]);
        }
        $user = auth()->user();
        if ($user instanceof Academic) {
            $user->load([
                'couponOwner',
                'cardApplicant',
                'cardApplicant.currentCardApplication.cardLastUpdate' //:status
            ]);
            if ($user->cardApplicant && !$user->cardApplicant->currentCardApplication) // we have define the new cardApplication to receive the expiration of the  valid application minimum
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

    public function store(): JsonResponse
    {
        if (session()->has('temporary_user')) {
            $temp = session('temporary_user');
            $modelClass = $temp['model'];
            $userTemp = $temp['user'];
            unset($userTemp['abilities']);
            $userTemp['password'] = '';
            $user = new $modelClass();
            foreach ($userTemp as $userTempKey => $userTempValue) {
                $user[$userTempKey] = $userTempValue;
            }
            $user->save();
            if ($user instanceof Academic) {
                $user->couponOwner()->create([]);
                $user->load('couponOwner');
            }
            session()->forget('temporary_user');
            session()->invalidate();
            Auth::guard($temp['guard'])->login($user);
//            session()->forget('temporary_user');
            session()->regenerate(true);
            return response()->json([
                'message' => 'User Created',
                'user' => $user,
                'model' => class_basename($temp['model']),

            ]);
        }
        return response()->json([], 404);

    }
}
