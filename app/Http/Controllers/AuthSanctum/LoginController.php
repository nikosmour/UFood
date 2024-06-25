<?php

namespace App\Http\Controllers\AuthSanctum;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//use Laravel\Sanctum\Http\Controllers\SanctumController;

class LoginController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt login using Laravel authentication
//        dd(config('auth.guards'));
        foreach (["couponStaffs", "academics", "cardApplicationStaffs", "entryStaffs"] as $guard) {

            if (Auth::guard($guard)->attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::guard($guard)->user();
                $user->couponOwner;
                $cardApplicant = $user->cardApplicant;
                if ($cardApplicant) $cardApplicant->address;
                return response()->json([
                    'success' => true,
                    'message' => 'Login Successful',
                    'abilities' => $user->getAbilities(),
                    'user' => $user,
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid email or password',
            "errors" => ["credentials" => ['invalid.credentials']]
        ], 422);
    }
}
