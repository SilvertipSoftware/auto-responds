<?php

namespace SilvertipSoftware\AutoResponds\Tests;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected $controllerRootNamespace = 'SilvertipSoftware\AutoResponds\Tests\Controllers';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('view.paths', ['tests/views']);
    }

    protected function addRoute($uri, $opts)
    {
        Route::namespace($this->controllerRootNamespace)->group(function () use ($uri, $opts) {
            Route::get($uri, $opts);
        });
    }

    public function getJs($uri)
    {
        $headers = $this->transformHeadersToServerVars([
            'Accept' => 'application/javascript'
        ]);

        return $this->call('GET', $uri, [], [], [], $headers, null);
    }
}
