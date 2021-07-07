<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

use Illuminate\Support\Facades\Storage;

class TrafficSourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $public_url = str_replace("public/", "storage/", $this->icon);
        $public_url = asset($public_url);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'script' => $this->script,
            'custom_params' => $this->custom_params,
            'ad_model' => $this->ad_model,
            'icon' => $public_url,
        ];
    }
}
