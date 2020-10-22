<?php

namespace SilvertipSoftware\AutoResponds\Tests\Controllers\Subspace;

use Illuminate\Support\Fluent;
use SilvertipSoftware\AutoResponds\Tests\Controllers\BaseController;

class ProjectsController extends BaseController
{
    public function index()
    {
        $this->projects = collect([
            $this->first(),
            $this->second()
        ]);
    }

    public function show()
    {
        $this->project = $this->first();
    }

    protected function first() {
        return new Fluent([
            'id' => 1,
            'name' => 'First'
        ]); 
    }

    protected function second() {
        return new Fluent([
            'id' => 2,
            'name' => 'Second'
        ]); 
    }
}
