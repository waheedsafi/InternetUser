<?php

namespace App\Providers;

use App\Http\Middleware\CheckAccess;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Route::pushMiddlewareToGroup('api', EnsureFrontendRequestsAreStateful::class);
         $router = $this->app->make(\Illuminate\Routing\Router::class);
    $router->aliasMiddleware('check.access', \App\Http\Middleware\CheckAccess::class);
    }
}
