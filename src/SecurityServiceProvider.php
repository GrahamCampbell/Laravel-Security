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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

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
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/security.php');

        $this->publishes([$source => config_path('security.php')]);

        $this->mergeConfigFrom($source, 'security');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerSecurity($this->app);
    }

    /**
     * Register the security class.
     *
     * @param Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function registerSecurity(Application $app)
    {
        $app->singleton('security', function ($app) {
            $evil = $app->config->get('security.evil');

            return new Security($evil);
        });

        $app->alias('security', 'GrahamCampbell\Security\Security');
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
