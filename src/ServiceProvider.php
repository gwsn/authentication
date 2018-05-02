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

        $this->loadViewsFrom(__DIR__.'/Resources/views', 'gwsn.authentication');


        $this->app['router']->aliasMiddleware('account.auth' , \Gwsn\Authentication\Middleware\AuthenticationMiddleware::class);
        $this->app['router']->aliasMiddleware('account.email.verified' , \Gwsn\Authentication\Middleware\AccountVerifiedMiddleware::class);


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


