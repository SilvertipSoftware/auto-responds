<?php

namespace SilvertipSoftware\AutoResponds\Tests;

use Illuminate\Support\Facades\Request;
use InvalidArgumentException;

class JsonResponseTest extends TestCase
{

    public function testGetsBasicSingularJson()
    {
        $this->addRoute('/', ['uses' => 'Subspace\AccountsController@show', 'as' => 'accounts.show']);

        $this->getJson('/')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'First'
                ]
            ]);
    }

    public function testCustomSubjectTag()
    {
        $this->addRoute('/', ['uses' => 'Subspace\DocsController@show', 'as' => 'docs.show']);

        $this->getJson('/')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'First'
                ]
            ]);
    }


    public function testGetsBasicCollectionJson()
    {
        $this->addRoute('/', ['uses' => 'Subspace\AccountsController@index', 'as' => 'accounts.index']);

        $this->getJson('/')
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'First'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Second'
                    ]
                ]
            ]);
    }

    public function testSingularWithResource()
    {
        $this->addRoute('/', ['uses' => 'Subspace\PeopleController@show', 'as' => 'people.show']);

        $this->getJson('/')
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'name' => 'First',
                    'login_count' => 5
                ]
            ]);
    }

    public function testSingularWithResourceAndCustomWrapper()
    {
        $this->addRoute('/', ['uses' => 'Subspace\PeopleController@wrappedShow', 'as' => 'people.show']);

        $this->getJson('/')
            ->assertOk()
            ->assertJson([
                'user' => [
                    'id' => 1,
                    'name' => 'First',
                    'login_count' => 5
                ]
            ]);
    }

    public function testCollectionWithSingularResource()
    {
        $this->addRoute('/', ['uses' => 'Subspace\PeopleController@index', 'as' => 'people.index']);

        $this->getJson('/')
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'First',
                        'login_count' => 5
                    ],
                    [
                        'id' => 2,
                        'name' => 'Second',
                        'login_count' => 6
                    ]
                ]
            ]);
    }

    public function testCollectionResource() {
        $this->addRoute('/', ['uses' => 'Subspace\ProjectsController@index', 'as' => 'projects.index']);

        $this->getJson('/')
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'id' => 1,
                        'name' => 'First'
                    ],
                    [
                        'id' => 2,
                        'name' => 'Second'
                    ]
                ],
                'link' => '/projects'
            ]);  
    }
}
