<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Pagination\Paginator;

use App\Http\Middleware\RoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services
        $this->app->singleton(\App\Services\DatabaseProcedureService::class);
        $this->app->singleton(\App\Services\DatabaseFunctionService::class);
        $this->app->singleton(\App\Services\LogActivityService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register role middleware alias here so we don't need to modify Kernel.php
        if ($this->app->runningInConsole() === false) {
            $router = $this->app->make(Router::class);
            $router->aliasMiddleware('role', RoleMiddleware::class);
        }
    }
}
