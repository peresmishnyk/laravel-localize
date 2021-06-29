<?php

namespace Peresmishnyk\LaravelLocalize\Facades;

use Illuminate\Support\Facades\Facade;

class LocalizeRoute extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'localize-route';
    }
}
