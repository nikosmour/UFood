<?php

namespace App\Providers;

use App\Services\Auth\JwtGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
//        $this->registerPolicies();
//        foreach (config('auth.guards') as $guard=>$value) {
//            Auth::extend($guard, function ($app, $name, array $config) {
//                // Return an instance of Illuminate\Contracts\Auth\Guard...
//
//                return new JwtGuard(Auth::createUserProvider($config['provider']));
//            });
//        }
    }
}
