<?php

namespace App\Http\Controllers\AuthSanctum;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle user login.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validate the request
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

            // Call the service to perform login
        $authResult = $this->userService->logIn($credentials);
        if (array_key_exists('error', $authResult))
            return response()->json([
                'message' => 'Invalid email or password.',
                'errors' => ['email' => ['Invalid credentials.']]
            ], 422);
        session(['department' => $authResult['department']]);
        // Fetch user by email and create session
        /** @var User $user */
        $user = Auth::guard($authResult['guard'])->getProvider()->retrieveByCredentials(['email' => $authResult['email']]) ?? $this->createUser($authResult);
        Auth::guard($authResult['guard'])->login($user);

        // Regenerate session here
        $request->session()->regenerate();

        return response()->json([], 204);
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->userService->logout($request);
    }

    /**
     * @param array $authResult
     * @return mixed
     */
    private function createUser(array $authResult): mixed
    {
        $modelClass = Auth::guard($authResult['guard'])->getProvider()->getModel();
        $user = new $modelClass();

// Assign attributes to the new user
        $user->name = $authResult['name'];
        if (array_key_exists('is_active', $authResult))
            $user->is_active = $authResult['is_active'];
        $user->a_m = $authResult['a_m'];
        if (array_key_exists('academic_id', $authResult))
            $user->academic_id = $authResult["academic_id"];
        $user->email = $authResult['email'];
        $user->status = $authResult['status'];
        $user->save();
        return $user;
    }
}
