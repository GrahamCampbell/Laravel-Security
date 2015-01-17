<?php

/*
 * This file is part of Laravel Security.
 *
 * (c) Graham Campbell <graham@mineuk.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Security;

use Orchestra\Support\Providers\ServiceProvider;

/**
 * This is the security service provider class.
 *
 * @author Graham Campbell <graham@mineuk.com>
 */
class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->addConfigComponent('graham-campbell/security', 'graham-campbell/security', realpath(__DIR__.'/../config'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSecurity();
    }

    /**
     * Register the security class.
     *
     * @return void
     */
    protected function registerSecurity()
    {
        $this->app->singleton('security', function ($app) {
            $evil = $app['config']['graham-campbell/security::evil'];

            return new Security($evil);
        });

        $this->app->alias('security', 'GrahamCampbell\Security\Security');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'security',
        ];
    }
}
