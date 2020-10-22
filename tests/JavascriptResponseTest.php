<?php

namespace SilvertipSoftware\AutoResponds\Tests;

class JavascriptResponseTest extends TestCase
{

    public function testGetsActionBasedJsView()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@basic']);

        $this->getJs('/')
            ->assertOk()
            ->assertSee('view = "objects.js.basic"');
    }

    public function testJsResponsesAreWrapped()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@basic']);

        $this->getJs('/')
            ->assertOk()
            ->assertSee('(function')
            ->assertSee(')()');
    }

    public function testJsRedirectsAreHandled()
    {
        $this->addRoute('/', ['uses' => 'ObjectsController@redirect']);

        $this->getJs('/')
            ->assertOk()
            ->assertSee('redirectTo("http://localhost/alternate")');
    }
}
