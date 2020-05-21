<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingResource extends JsonResource
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
            'name' => $this->name,
            'campaign' => [
                'id' => $this->campaign->id,
                'name' => $this->campaign->name,
            ],
            'form' => [
                'id' => $this->form->id,
                'name' => $this->form->name,
            ],
            'tag' => [
                'id' => $this->tag->id,
                'name' => $this->tag->name,
            ],
            'url' => $this->url,
            'country' => $this->country,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
