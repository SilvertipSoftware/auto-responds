<?php

namespace SilvertipSoftware\AutoResponds\Tests\Controllers\Subspace;

use Illuminate\Support\Fluent;
use SilvertipSoftware\AutoResponds\Tests\Controllers\BaseController;

class AccountsController extends BaseController
{
    public function index()
    {
        $this->one = 1;
        $this->two = 2;
        $this->accounts = collect([
            $this->first(),
            $this->second()
        ]);
    }

    public function show()
    {
        $this->account = $this->first();
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

    protected function getVariablesToShare()
    {
        return ['one'];
    }
}
