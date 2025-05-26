<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel Breeze (and others) for redirection after login.
     */
    public const HOME = '/redirect';

    /**
     * Define your route model bindings, pattern filters, and more.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        // Define routes
        $this->routes(function () {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        });

        // Role-based redirect logic
        Route::middleware('web')->group(function () {
            Route::get('/redirect', function () {
                $user = Auth::user();

                if ($user && $user->hasRole('grower')) {
                    return redirect()->route('grower.dashboard');
                }

                // Default fallback
                return redirect('/dashboard');
            });
        });
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}