<?php

namespace App\Providers;

//use Illuminate\Cache\RateLimiter;
use App\Models\Book;
use App\Models\Reservation;
use App\Policies\BookPolicy;
use App\Policies\ReservationPolicy;
use Illuminate\Auth\Access\Gate;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;


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
        RateLimiter::for('reservation', function (Request $request) {
           return $request->user() ?
               Limit::perMinute(10)->by($request->ip()) :
               Limit::perMinute(1)->by($request->ip());
        });
    }
}
