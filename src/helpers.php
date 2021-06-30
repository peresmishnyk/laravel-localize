<?php

if (! function_exists('route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param string $name
     * @param array $parameters
     * @param bool $absolute
     * @param null|string $locale
     *
     * @return string
     */
    function route($name, $parameters = [], $absolute = true, $locale = null)
    {
        return app('url')->route($name, $parameters, $absolute, $locale);
    }
}

if (! function_exists('localize_route')) {
    /**
     * Generate the URL to a named route.
     *
     * @param string $locale
     * @param string $name
     * @param array $parameters
     * @param bool $absolute
     *
     * @return string
     */
    function localized_route($locale, $name, $parameters = [], $absolute = true)
    {
        return app('url')->route($name, $parameters, $absolute, $locale);
    }
}
