<?php

namespace App\Services;

use App\Http\Controllers\Auth\LoginController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class UserService
{
    /**
     * Attempt to log in the user.
     *
     * @param array $credentials
     * @return array
     */
    public function logIn(array $credentials): array
    {
        $originalConnection = config('database.default');
        DB::setDefaultConnection('secondary_mysql');
        // Loop through all available guards and attempt login
        foreach (config('auth.guards') as $guard => $temp) {
            if ($guard !== 'sanctum' && Auth::guard($guard)->attempt($credentials)) {
                // Optionally, return user data or a token if needed
                /** @var User $user */
                $user = Auth::guard($guard)->user();
                DB::setDefaultConnection($originalConnection);
                return [
                    'email' => $user->email,
                    'name' => $user->name,
                    'status' => $user->status,
                    'a_m' => $user->a_m ?? null,
                    "academic_id" => $user->academic_id ?? null,
                    'is_active' => $user->is_active ?? true,
                    'department' => $user->cardApplicant()->withOnly('departmentRelation')->first()?->department ?? null,
                    'guard' => $guard,
                ];
            }
        }
        DB::setDefaultConnection($originalConnection);
        return [
            'error' => 'invalid_credentials',
        ];
    }

    public function logOut(Request $request)
    {
        return app(LoginController::class)->logout($request);
    }
}
