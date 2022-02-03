<?php

namespace App\Http\Resources\api\third;

use Illuminate\Http\Resources\Json\JsonResource;

class FacebookCopyResource extends JsonResource
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
            'number' => $this->number,
            'ad_title' => $this->ad_title,
            'ad_text' => $this->ad_text,
        ];
    }
}
