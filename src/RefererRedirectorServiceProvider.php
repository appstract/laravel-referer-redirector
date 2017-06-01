<?php

namespace Appstract\RefererRedirector;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Appstract\RefererRedirector\Middleware\RedirectReferer;

class RefererRedirectorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(Router $router)
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->commands([
                \Appstract\RefererRedirector\Console\MakeCommand::class,
                \Appstract\RefererRedirector\Console\RemoveCommand::class,
                \Appstract\RefererRedirector\Console\ListCommand::class,
            ]);
        }

        $this->registerMiddleware($router);
    }

    /**
     * Register and publish migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register middleware.
     *
     * @param  object $router
     * @return void
     */
    protected function registerMiddleware($router)
    {
        if (method_exists($router, 'aliasMiddleware')) {
            $router->aliasMiddleware('redirect-referer', RedirectReferer::class);
        } else {
            $router->middleware('redirect-referer', RedirectReferer::class);
        }
    }
}
