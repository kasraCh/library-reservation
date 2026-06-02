<?php

namespace App\Providers;

// use Illuminate\Cache\RateLimiter;
use App\Events\ReservationCreated;
use App\Listeners\LogReservationActivity;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
//        \Event::listen(
//            ReservationCreated::class,
//            LogReservationActivity::class,
//        );
        RateLimiter::for('reservation', function (Request $request) {
            return $request->user() ?
                Limit::perMinute(10)->by($request->ip()) :
                Limit::perMinute(1)->by($request->ip());
        });
    }
}
