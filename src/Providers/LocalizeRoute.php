<?php


namespace Peresmishnyk\LaravelLocalize\Providers;


use Illuminate\Support\Facades\Route;

class LocalizeRoute
{
    protected static $config_key;

    public function __construct($config_key)
    {
        $this::$config_key = $config_key;
    }

    public function getConfig($key = ''){
        $key = trim(static::$config_key . '.' . $key, '.');

        return \Config::get($key);
    }

    public static function byDomain(...$args)
    {
        list($params, $closure) = static::parse_params($args, __FUNCTION__);
        static::domain($params, $closure, true);
    }

    public static function byPrefixWithoutDefault(...$args)
    {
        list($params, $closure) = static::parse_params($args, __FUNCTION__);
        static::prefix($params, $closure, true);
    }

    public static function byPrefix(...$args)
    {
        list($params, $closure) = static::parse_params($args, __FUNCTION__);
        static::prefix($params, $closure, false);
    }

    private static function prefix(array $params, \Closure $closure, bool $skip_prefix_for_default)
    {
        $use_locale_middleware = $params['use_locale_middleware'] ?? config('localized-route.use_locale_middleware', true);
//        $register_redirect_for_default = $params['redirect_for_default'] ??

        // Save app locale
        $saved_app_locale = app()->getLocale();

        foreach ($params['locale_keys'] as $key => $locale) {
            app()->setLocale($locale);

            $route_group_params = [
                'prefix' => $key,
                'as' => $locale . '.',
                'locale' => $locale,
                'middleware' => $use_locale_middleware ? ['locale'] : []
            ];

            if ($params['default_locale'] === $locale && $skip_prefix_for_default) {
                unset($route_group_params['prefix']);
            }

            Route::group($route_group_params, $closure);
        }

        // Restore saved app locale
        app()->setLocale($saved_app_locale);
    }

    private static function domain(array $params, \Closure $closure){
        // Save app locale
        $saved_app_locale = app()->getLocale();

        foreach ($params['locale_keys'] as $key => $locale) {
            app()->setLocale($locale);

            $domain = str_replace('{}', $key, $params['domain_pattern']);

            $route_group_params = [
                'domain' => $domain,
                'as' => $locale . '.',
                'locale' => $locale,
                //'middleware' => $use_locale_middleware ? ['locale'] : []
            ];

            Route::group($route_group_params, $closure);
        }

        // Restore saved app locale
        app()->setLocale($saved_app_locale);
    }

    private static function parse_params($args, $caller): array
    {
        $args = array_pad($args, -2, []);
        if (is_array($args[0]) && $args[1] instanceof \Closure) {
            list($params, $closure) = $args;
        } else {
            throw new \InvalidArgumentException("Use LocalizeRoute::{$caller}(Closure \$closure) or LocalizeRoute::{$caller}(array \$params, Closure \$closure)");
        }
        // Merge config with runtime params
        $params = array_merge(config(static::$config_key), $params);
        return [$params, $closure];
    }
}
