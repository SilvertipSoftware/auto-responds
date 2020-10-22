<?php

namespace SilvertipSoftware\AutoResponds\Tests\Controllers;

use SilvertipSoftware\AutoResponds\AutoResponds;

class ObjectsController extends BaseController
{
    protected $missingViewName = null;

    public function basic()
    {
        $this->one = 1;
        $this->two = 2;
    }

    public function usingRouteName()
    {
        // use the route name instead
        $this->useActionForViewName = false;
    }

    public function missingFallback()
    {
        $this->missingViewName = 'missing';
    }

    public function reallyMissingView() {}

    public function redirect()
    {
        return redirect('/alternate');
    }

    public function specificView()
    {
        $this->viewNameForResponse = 'specific';
    }

    public function getViewNameForMissingView()
    {
        return $this->missingViewName;
    }
}
