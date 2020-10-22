<?php

namespace SilvertipSoftware\AutoResponds\Tests\Controllers;

use Illuminate\Routing\Controller;
use SilvertipSoftware\AutoResponds\AutoResponds;

class BaseController extends Controller
{
    use AutoResponds;

    protected function controllerRootNamespace()
    {
        return 'SilvertipSoftware\AutoResponds\Tests\Controllers';
    }  
}
