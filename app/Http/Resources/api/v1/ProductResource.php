<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tag' => [
                'id' => $this->tag->id,
                'name' => $this->tag->name,
            ],
            'advertiser' => [
                'id' => $this->advertiser->id,
                'name' => $this->advertiser->name,
            ],
            'country' => $this->country,
            'quantity' => json_decode(json_encode($this->quantity), false),
            'info' => json_decode(json_encode($this->info), false),
        ];
    }
}
