<?php
/**
 * Just another summery
 * @author      Michkire Dmytro <michkire@gmail.com>
 * @copyright   Copyright (c) 2021, Peresmishnyk
 **/

namespace Peresmishnyk\LaravelLocalize\Providers;

use Closure;
use Config;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;

/**
 * Class LocalizeRoute
 */
class LocalizeRoute
{
    /**
     * @var string Package config key
     */
    protected static $configKey;

    /**
     * LocalizeRoute constructor.
     * @param string $configKey
     */
    public function __construct(string $configKey)
    {
        $this::$configKey = $configKey;
    }

    /**
     * @param ...$args
     */
    public static function byDomain(...$args)
    {
        $callerName = __FUNCTION__;
        list($params, $closure) = static::parseParams($args, $callerName);
        static::domain($params, $closure);
    }

    /**
     * @param $args
     * @param $caller
     * @return array
     */
    private static function parseParams($args, $caller): array
    {
        $args = array_pad($args, -2, []);
        if (is_array($args[0]) && $args[1] instanceof Closure) {
            list($params, $closure) = $args;
        } else {
            throw new InvalidArgumentException("Use LocalizeRoute::$caller(Closure \$closure) or LocalizeRoute::$caller(array \$params, Closure \$closure)");
        }
        // Merge config with runtime params
        $params = array_merge(config(static::$configKey), $params);
        return [$params, $closure];
    }

    /**
     * @param array $params
     * @param Closure $closure
     */
    private static function domain(array $params, Closure $closure)
    {
        // Save app locale
        $savedAppLocale = app()->getLocale();

        foreach ($params['locale_keys'] as $key => $locale) {
            app()->setLocale($locale);

            $domain = str_replace('{}', $key, $params['domain_pattern']);

            $routeGroupParams = [
                'domain' => $domain,
                'as' => $locale . '.',
                'locale' => $locale,
                //'middleware' => $use_locale_middleware ? ['locale'] : []
            ];

            Route::group($routeGroupParams, $closure);
        }

        // Restore saved app locale
        app()->setLocale($savedAppLocale);
    }

    /**
     * @param ...$args
     */
    public static function byPrefixWithoutDefault(...$args)
    {
        $callerName = __FUNCTION__;
        list($params, $closure) = static::parseParams($args, $callerName);
        static::prefix($params, $closure, true);
    }

    /**
     * @param array $params
     * @param Closure $closure
     * @param bool $skipPrefixForDefault
     */
    private static function prefix(array $params, Closure $closure, bool $skipPrefixForDefault)
    {
        $useLocaleMiddleware = $params['use_locale_middleware'] ?? config('localized-route.use_locale_middleware', true);
//        $register_redirect_for_default = $params['redirect_for_default'] ??

        // Save app locale
        $savedAppLocale = app()->getLocale();

        foreach ($params['locale_keys'] as $key => $locale) {
            app()->setLocale($locale);

            $routeGroupParams = [
                'prefix' => $key,
                'as' => $locale . '.',
                'locale' => $locale,
                'middleware' => $useLocaleMiddleware ? ['locale'] : []
            ];

            if ($params['default_locale'] === $locale && $skipPrefixForDefault) {
                unset($routeGroupParams['prefix']);
            }

            Route::group($routeGroupParams, $closure);
        }

        // Restore saved app locale
        app()->setLocale($savedAppLocale);
    }

    /**
     * @param ...$args
     */
    public static function byPrefix(...$args)
    {
        $callerName = __FUNCTION__;
        list($params, $closure) = static::parseParams($args, $callerName);
        static::prefix($params, $closure, false);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getConfig(string $key = '')
    {
        $key = trim(static::$configKey . '.' . $key, '.');

        return Config::get($key);
    }
}
