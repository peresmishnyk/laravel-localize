<?php

/**
 * Service facade
 *
 * @author      Michkire Dmytro <michkire@gmail.com>
 * @copyright   Copyright (c) 2021, Peresmishnyk
 **/

namespace Peresmishnyk\LaravelLocalize\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class LocalizeRoute
 * @package Peresmishnyk\LaravelLocalize
 */
class LocalizeRoute extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return 'localize-route';
    }
}
