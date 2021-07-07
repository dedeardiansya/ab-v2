<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::domain('admin.' . env('APP_DOMAIN'))
            ->middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));

        Route::domain('admin.' . env('APP_DOMAIN'))
            ->middleware(['web', 'auth', 'can:admin access'])
            ->namespace($this->namespace . '\Admin')
            ->group(base_path('routes/admin.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::domain('api.' . env('APP_DOMAIN'))
            ->middleware('api')
            ->as('api.')
            ->namespace($this->namespace . '\Api\Auth')
            ->group(base_path('routes/auth_api.php'));

        Route::domain('api.' . env('APP_DOMAIN'))
            ->middleware('api')
            ->as('api.')
            ->namespace($this->namespace . '\Api')
            ->group(base_path('routes/api.php'));

        Route::domain('api.' . env('APP_DOMAIN'))
            ->prefix('admin')
            ->middleware(['api', 'auth:api', 'can:admin access'])
            ->as('api.admin.')
            ->namespace($this->namespace . '\Admin\Api')
            ->group(base_path('routes/admin_api.php'));
    }
}
