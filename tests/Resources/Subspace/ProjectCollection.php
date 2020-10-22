<?php

namespace SilvertipSoftware\AutoResponds\Tests\Resources\Subspace;

use Illuminate\Http\Resources\Json\ResourceCollection;


class ProjectCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            'link' => '/projects'
        ];
    }
}
