<?php

namespace Peresmishnyk\LaravelLocalize;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Peresmishnyk\BackpackSettings\Settings;
use Peresmishnyk\LaravelLocalize\Providers\LocalizeRoute;

class AddonServiceProvider extends ServiceProvider
{
    use AutomaticServiceProvider;

    protected $vendorName = 'peresmishnyk';
    protected $packageName = 'laravel-localize';
    protected $commands = [];

    //$this->app->singleton('settings', function ($app) {
    //    return new Settings('backpack.settings');
    //});
    //
    //$this->app->booting(function () {
    //    $loader = AliasLoader::getInstance();
    //    $loader->alias('Settings', \Peresmishnyk\BackpackSettings\Facades\Settings::class);
    //});

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

        $this->booting(\Closure::fromCallable([$this, 'booting_callback']));
        $this->booted(\Closure::fromCallable([$this, 'booted_callback']));
    }

    private function booting_callback()
    {
        // Register Facade
        dump('Register Facade');
        $loader = AliasLoader::getInstance();
        $loader->alias('LocalizeRoute', \Peresmishnyk\LaravelLocalize\Facades\LocalizeRoute::class);

        dump('booting');
    }

    private function booted_callback()
    {
        dump('booted');
    }

}
