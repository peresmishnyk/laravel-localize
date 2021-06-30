<?php

namespace Tests\Unit\Route;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\View;
use Peresmishnyk\LaravelLocalize\Facades\LocalizeRoute;
use Peresmishnyk\LaravelLocalize\Providers\UrlGenerator;
use Peresmishnyk\LaravelLocalize\Tests\BaseTest;

class RouteTest extends BaseTest
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            'Peresmishnyk\LaravelLocalize\AddonServiceProvider',
        ];
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_url_service_provider_override()
    {
        $this->assertTrue($this->app->make('url') instanceof UrlGenerator);
    }

    public function test_aliases(){
        $test_aliases = [
            'LocalizeRoute' => LocalizeRoute::class,
            'View' => View::class,
        ];
        $loader = AliasLoader::getInstance();
        $aliases = $loader->getAliases();
        foreach ($test_aliases as $key=>$class_name){
            $this->assertArrayHasKey($key, $aliases);
            //$this->assertEquals($class_name, $aliases[$key]);
        }
    }
}
