<?php

namespace App\Providers;

use App\Models\Profile;
use App\Observers\ProfileObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Profile::observe(ProfileObserver::class);
    }
}
