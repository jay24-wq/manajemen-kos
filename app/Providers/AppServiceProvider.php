<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Rental;
use App\Observers\RentalObserver;

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
        Rental::observe(RentalObserver::class);
    }
}
