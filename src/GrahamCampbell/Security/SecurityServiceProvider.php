<?php namespace GrahamCampbell\Security;

use Illuminate\Support\ServiceProvider;

class SecurityServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        $this->package('graham-campbell/security');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->app['security'] = $this->app->share(function($app) {
            return new Classes\Security;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array();
    }
}
