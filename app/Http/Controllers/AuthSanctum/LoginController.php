<?php

namespace App\Http\Controllers\AuthSanctum;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        foreach (config('auth.guards') as $guard => $value) {

            if (Auth::guard($guard)->attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::guard($guard)->user();
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
            'guard' => config('auth.guards'),
        ], 401);
    }
}
