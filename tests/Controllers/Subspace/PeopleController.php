<?php

namespace SilvertipSoftware\AutoResponds\Tests\Controllers\Subspace;

use Illuminate\Support\Fluent;
use SilvertipSoftware\AutoResponds\Tests\Controllers\BaseController;

class PeopleController extends BaseController
{
    public function index()
    {
        $this->people = collect([
            $this->first(),
            $this->second()
        ]);
    }

    public function show()
    {
        $this->person = $this->first();
    }

    public function wrappedShow()
    {
        $this->person = $this->first();
        $this->customWrapper = 'user';
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

    protected function wrapperForResource($resource, $propName)
    {
        if (isset($this->customWrapper)) {
            return $this->customWrapper;
        }

        return parent::wrapperForResource($resource, $propName);
    }
}
