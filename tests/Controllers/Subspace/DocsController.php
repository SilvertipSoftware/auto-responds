<?php

namespace SilvertipSoftware\AutoResponds\Tests\Controllers\Subspace;

use Illuminate\Support\Fluent;
use SilvertipSoftware\AutoResponds\Tests\Controllers\BaseController;

class DocsController extends BaseController
{
    public function index()
    {
        $this->documents = collect([
            $this->first(),
            $this->second()
        ]);
    }

    public function show()
    {
        $this->document = $this->first();
    }

    // Customize the model tag/name we use, in case the controller/route name doesn't match
    protected function getSubjectResourceTag() {
        return 'document';
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
