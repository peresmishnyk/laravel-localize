<?php
/**
 * Service provider
 *
 * @package     Peresmishnyk\LaravelLocalize
 * @author      Michkire Dmytro <michkire@gmail.com>
 * @copyright   Copyright (c) 2021, Peresmishnyk
 **/

namespace Peresmishnyk\LaravelLocalize;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Peresmishnyk\LaravelLocalize\Providers\LocalizeRoute;
use Peresmishnyk\LaravelLocalize\Providers\UrlGenerator;

/**
 * Class AddonServiceProvider
 * {@inheritdoc }
 */
class AddonServiceProvider extends ServiceProvider
{
    use AutomaticServiceProvider;

    /**
     * @var string Vendor name
     */
    protected $vendorName = 'peresmishnyk';
    /**
     * @var string Package name
     */
    protected $packageName = 'laravel-localize';
    /**
     * Package commands array
     * Define package commands
     * @var array
     */
    protected $commands = [];

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        if ($this->packageDirectoryExistsAndIsNotEmpty('config')) {
            $this->mergeConfigFrom($this->packageConfigFile(), $this->vendorNameDotPackageName());
        }

        $this->app->singleton('localize-route', function () {
            return new LocalizeRoute($this->vendorNameDotPackageName());
        });

        $this->registerUrlGenerator();
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->packageDirectoryExistsAndIsNotEmpty('bootstrap') &&
            file_exists($helpers = $this->packageHelpersFile())) {
            require $helpers;
        }

        if ($this->packageDirectoryExistsAndIsNotEmpty('resources/lang')) {
            $this->loadTranslationsFrom($this->packageLangsPath(), $this->vendorNameDotPackageName());
        }

        if ($this->packageDirectoryExistsAndIsNotEmpty('resources/views')) {
            // Load published views
            $this->loadViewsFrom($this->publishedViewsPath(), $this->vendorNameDotPackageName());

            // Fallback to package views
            $this->loadViewsFrom($this->packageViewsPath(), $this->vendorNameDotPackageName());
        }

        if ($this->packageDirectoryExistsAndIsNotEmpty('database/migrations')) {
            $this->loadMigrationsFrom($this->packageMigrationsPath());
        }

        if ($this->packageDirectoryExistsAndIsNotEmpty('routes')) {
            $this->loadRoutesFrom($this->packageRoutesFile());
        }

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }

        // Set app locale to default
        $this->app->setLocale(config($this->vendorNameDotPackageName())['default_locale']);

        // Register Facade
        $loader = AliasLoader::getInstance();
        $loader->alias('LocalizeRoute', LocalizeRoute::class);
    }

    /**
     * Register the URL generator service.
     *
     * The UrlGenerator class that is instantiated is determined
     * by the "use" statement at the top of this file.
     *
     * This method is an exact copy from:
     * \Illuminate\Routing\RoutingServiceProvider
     *
     * @return void
     */
    protected function registerUrlGenerator()
    {
        $this->app->singleton('url', function ($app) {
            $routes = $app['router']->getRoutes();

            // The URL generator needs the route collection that exists on the router.
            // Keep in mind this is an object, so we're passing by references here
            // and all the registered routes will be available to the generator.
            $app->instance('routes', $routes);

            $url = new UrlGenerator(
                $routes,
                $app->rebinding(
                    'request',
                    $this->requestRebinder()
                ),
                $app['config']['app.asset_url']
            );

            // Next we will set a few service resolvers on the URL generator so it can
            // get the information it needs to function. This just provides some of
            // the convenience features to this URL generator like "signed" URLs.
            $url->setSessionResolver(function () {
                return $this->app['session'] ?? null;
            });

            $url->setKeyResolver(function () {
                return $this->app->make('config')->get('app.key');
            });

            // If the route collection is "rebound", for example, when the routes stay
            // cached for the application, we will need to rebind the routes on the
            // URL generator instance so it has the latest version of the routes.
            $app->rebinding('routes', function ($app, $routes) {
                $app['url']->setRoutes($routes);
            });

            return $url;
        });
    }

    /**
     * Get the URL generator request rebinder.
     *
     * This method is an exact copy from:
     * \Illuminate\Routing\RoutingServiceProvider
     *
     * @return \Closure
     */
    protected function requestRebinder()
    {
        return function ($app, $request) {
            $app['url']->setRequest($request);
        };
    }
}
