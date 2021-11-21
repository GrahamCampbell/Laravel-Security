<?php

declare(strict_types=1);

/*
 * This file is part of Laravel Security.
 *
 * (c) Graham Campbell <hello@gjcampbell.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GrahamCampbell\Security;

use GrahamCampbell\SecurityCore\Security;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

/**
 * This is the security service provider class.
 *
 * @author Graham Campbell <hello@gjcampbell.co.uk>
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
        $source = realpath($raw = __DIR__.'/../config/security.php') ?: $raw;

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('security.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('security');
        }

        $this->mergeConfigFrom($source, 'security');
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
        $this->app->singleton('security', function (Container $app) {
            $evil = $app->config->get('security.evil');
            $replacement = $app->config->get('security.replacement');

            return Security::create($evil, $replacement);
        });

        $this->app->alias('security', Security::class);
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
