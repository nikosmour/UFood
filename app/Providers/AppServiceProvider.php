<?php

namespace App\Providers;

use App\Models\CouponOwner;
use App\Models\UsageCard;
use App\Observers\CouponOwnerObserver;
use App\Observers\UsageCardObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        if (!$this->app->environment('production')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Model::preventLazyLoading(!$this->app->isProduction());
        $paths = glob(database_path('migrations/*'), GLOB_ONLYDIR);

        $this->loadMigrationsFrom(array_merge([database_path('migrations')], $paths));
        CouponOwner::observe(CouponOwnerObserver::class);
        UsageCard::observe(UsageCardObserver::class);
    }
}
