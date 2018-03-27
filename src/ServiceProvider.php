<?php
namespace Gwsn\Authentication;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider {
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Gwsn\Authentication\Controllers';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Database/Migrations' => $this->app->databasePath() . '/migrations'
        ], 'migrations');

        $this->app['router']->aliasMiddleware('authentication' , \Gwsn\Authentication\Middleware\AuthenticationMiddleware::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mapAccountsRoutes();
    }

    public function mapAccountsRoutes() {
        Route::prefix('api')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/Routes/Api.php');


    }
}


