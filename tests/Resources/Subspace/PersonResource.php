<?php

namespace SilvertipSoftware\AutoResponds\Tests\Resources\Subspace;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'login_count' => strlen($this->name) 
        ];
    }
}
