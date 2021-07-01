<?php
/**
 * @package     Peresmishnyk\LaravelLocalize
 * @author      Michkire Dmytro <michkire@gmail.com>
 * @copyright   Copyright (c) 2021, Peresmishnyk
 **/

namespace Peresmishnyk\LaravelLocalize;

/**
 * This trait automatically loads package stuff, if they're present
 * in the expected directory. Stick to the conventions and
 * your package will "just work". Feel free to override
 * any of the methods below in your ServiceProvider
 * if you need to change the paths.
 */
trait AutomaticServiceProvider
{
    /**
     * AutomaticServiceProvider constructor.
     * @param $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->path = __DIR__.'/..';
    }

    /**
     * -------------------------
     * SERVICE PROVIDER DEFAULTS
     * -------------------------
     */

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
    }

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
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        if ($this->packageDirectoryExistsAndIsNotEmpty('config')) {
            $this->publishes([
                $this->packageConfigFile() => $this->publishedConfigFile(),
            ], 'config');
        }

        // Publishing the views.
        if ($this->packageDirectoryExistsAndIsNotEmpty('resources/views')) {
            $this->publishes([
                $this->packageViewsPath() => $this->publishedViewsPath(),
            ], 'views');
        }

        // Publishing assets.
        if ($this->packageDirectoryExistsAndIsNotEmpty('resources/assets')) {
            $this->publishes([
                $this->packageAssetsPath() => $this->publishedAssetsPath(),
            ], 'assets');
        }

        // Publishing the translation files.
        if ($this->packageDirectoryExistsAndIsNotEmpty('resources/lang')) {
            $this->publishes([
                $this->packageLangsPath() => $this->publishedLangsPath(),
            ], 'lang');
        }

        // Registering package commands.
        if (!empty($this->commands)) {
            $this->commands($this->commands);
        }
    }

    /**
     * -------------------
     * CONVENIENCE METHODS
     * -------------------
     */

    protected function vendorNameDotPackageName()
    {
        return $this->vendorName.'.'.$this->packageName;
    }

    /**
     * @return string
     */
    protected function vendorNameSlashPackageName()
    {
        return $this->vendorName.'/'.$this->packageName;
    }

    // -------------
    // Package paths
    // -------------

    /**
     * @return string
     */
    protected function packageViewsPath()
    {
        return $this->path.'/resources/views';
    }

    /**
     * @return string
     */
    protected function packageLangsPath()
    {
        return $this->path.'/resources/lang';
    }

    /**
     * @return string
     */
    protected function packageAssetsPath()
    {
        return $this->path.'/resources/assets';
    }

    /**
     * @return string
     */
    protected function packageMigrationsPath()
    {
        return $this->path.'/database/migrations';
    }

    /**
     * @return string
     */
    protected function packageConfigFile()
    {
        return $this->path.'/config/'.$this->packageName.'.php';
    }

    /**
     * @return string
     */
    protected function packageRoutesFile()
    {
        return $this->path.'/routes/'.$this->packageName.'.php';
    }

    /**
     * @return string
     */
    protected function packageHelpersFile()
    {
        return $this->path.'/bootstrap/helpers.php';
    }

    // ---------------
    // Published paths
    // ---------------

    /**
     * @return string
     */
    protected function publishedViewsPath()
    {
        return base_path('resources/views/vendor/'.$this->vendorName.'/'.$this->packageName);
    }

    /**
     * @return string
     */
    protected function publishedConfigFile()
    {
        return config_path($this->vendorNameSlashPackageName().'.php');
    }

    /**
     * @return string
     */
    protected function publishedAssetsPath()
    {
        return public_path('vendor/'.$this->vendorNameSlashPackageName());
    }

    /**
     * @return string
     */
    protected function publishedLangsPath()
    {
        return resource_path('lang/vendor/'.$this->vendorName);
    }

    // -------------
    // Miscellaneous
    // -------------

    /**
     * @param $name
     * @return bool
     */
    protected function packageDirectoryExistsAndIsNotEmpty($name)
    {
        // check if directory exists
        if (!is_dir($this->path.'/'.$name)) {
            return false;
        }

        // check if directory has files
        foreach (scandir($this->path.'/'.$name) as $file) {
            if ($file != '.' && $file != '..' && $file != '.gitkeep') {
                return true;
            }
        }

        return false;
    }
}
