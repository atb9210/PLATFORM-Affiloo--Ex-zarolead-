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
        $client = new \Spatie\Dropbox\Client(env('DROPBOX_AUTH_TOKEN', ''));
        $icon_url = '';
        if ($this->icon != '') {
            $icon_url = $client->getTemporaryLink($this->icon);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $icon_url,
            'custom_params' => $this->custom_params,
            'ad_model' => $this->ad_model,
        ];
    }
}
