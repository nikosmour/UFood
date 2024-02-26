<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SelectLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('locale'))
            if ($lang = $request->getPreferredLanguage(
                config('app.available_locales')
            ))
                session()->put('locale', $lang);
            else
                session()->put('locale', config('app.locale'));


        app()->setLocale(session('locale'));

        return $next($request);
    }
}
