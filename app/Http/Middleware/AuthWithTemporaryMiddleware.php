<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;

class AuthWithTemporaryMiddleware extends BaseAuthenticate
{
    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param Request $request
     * @param array $guards
     * @return void
     *
     * @throws AuthenticationException
     */
    protected function authenticate($request, array $guards): void
    {
        // Allow access if a temporary user exists in the session
        if ($request->session()->has('temporary_user')) {
            return;
        }
        // Check if the user is authenticated in any guard
        parent::authenticate($request, $guards);
    }
}
