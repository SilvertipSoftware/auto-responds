<?php

namespace SilvertipSoftware\AutoResponds\Tests;

use Illuminate\Support\Facades\Request;
use InvalidArgumentException;

class HtmlResponseTest extends TestCase
{

    public function testGetsActionBasedView()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@basic']);

        $this->get('/')
            ->assertOk()
            ->assertSee('This is objects.basic');
    }

    public function testAllVariablesAreSharedByDefault()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@basic']);

        $this->get('/')
            ->assertOk()
            ->assertSee('one = 1')
            ->assertSee('two = 2');
    }

    public function testSharedVariablesCanBeRestricted()
    {
        $this->addRoute('/', ['uses' => 'Subspace\AccountsController@index']);

        $this->get('/')
            ->assertOk()
            ->assertSee('one = 1')
            ->assertSee('two = not defined');
    }

    public function testMissingFallback()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@missingFallback']);

        $this->get('/')
            ->assertOk()
            ->assertSee('This is a fallback view');
    }

    public function testReallyMissingViewsRaiseException()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@reallyMissingView']);

        $this->expectException(InvalidArgumentException::class);
        $this->get('/');
    }

    public function testRedirectsAreUntouched()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@redirect']);

        $this->get('/')
            ->assertRedirect('/alternate');
    }

    public function testCanSpecifyViewName()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@specificView']);

        $this->get('/')
            ->assertOk()
            ->assertSee('This is specific');
    }

    public function testCanGetRouteBasedView()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@usingRouteName', 'as' => 'usingRouteName']);
        $ret = $this->get('/')
            ->assertOk()
            ->assertSee('This is usingRouteName');
    }

    public function testNamespacedActions()
    {
        $this->addRoute('/', ['uses' => 'Subspace\AccountsController@index']);

        $this->get('/')
            ->assertOk()
            ->assertSee('This is subspace.accounts.index');
    }

    public function testOptionalFreshnessReturnsNotModified()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@basic']);
        Request::macro('isFresh', function () {
            static::$macros = [];
            return true;
        });

        $ret = $this->get('/')
            ->assertStatus(304)
            ->getContent();

        $this->assertEquals('', $ret);
    }
}
