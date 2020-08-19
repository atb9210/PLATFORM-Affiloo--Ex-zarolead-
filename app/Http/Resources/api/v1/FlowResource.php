<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class FlowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'id' => $this->user->email,
            ],
            'name' => $this->name,
            'type' => $this->type,
            'rules' => $this->rules,
            'path_name' => $this->path_name,
            'url' => $this->getPathnameUrl(),
        ];
    }
}
