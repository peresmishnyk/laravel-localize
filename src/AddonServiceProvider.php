<?php

namespace Peresmishnyk\LaravelLocalize;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Peresmishnyk\BackpackSettings\Settings;
use Peresmishnyk\LaravelLocalize\Providers\LocalizeRoute;
use Peresmishnyk\LaravelLocalize\Providers\UrlGenerator;

class AddonServiceProvider extends ServiceProvider
{
    use AutomaticServiceProvider;

    protected $vendorName = 'peresmishnyk';
    protected $packageName = 'laravel-localize';
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

        $this->app->singleton('localize-route', function ($app) {
            return new LocalizeRoute($this->vendorNameDotPackageName());
        });

        $this->registerUrlGenerator();

        $this->booting(\Closure::fromCallable([$this, 'booting_callback']));
        $this->booted(\Closure::fromCallable([$this, 'booted_callback']));
    }

    private function booting_callback()
    {
        // Set app locale to default
        $this->app->setLocale(config($this->vendorNameDotPackageName())['default_locale']);

        // Register Facade
        $loader = AliasLoader::getInstance();
        $loader->alias('LocalizeRoute', \Peresmishnyk\LaravelLocalize\Facades\LocalizeRoute::class);
    }

    private function booted_callback()
    {

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
                $routes, $app->rebinding(
                'request', $this->requestRebinder()
            ), $app['config']['app.asset_url']
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
