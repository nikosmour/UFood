<?php

namespace App\Http\Middleware;

use App\Enum\UserAbilityEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasAbility
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $abilityName
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $abilityName): mixed
    {
        /** @var UserAbilityEnum $ability */
        $ability = UserAbilityEnum::fromName($abilityName);
        abort_if(!$request->user()->hasAbility(UserAbilityEnum::fromName($abilityName)), 403, 'you don\'t have the ability to access this service');
        Auth::setUser($request->user()->getUserClassFromAbility($ability->UserClass()));
        return $next($request);
    }

}
