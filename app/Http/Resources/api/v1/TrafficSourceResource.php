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
        // Param validation
        $params = $request->validate([
            'icons' => 'boolean',
        ]);

        $toReturn = [
            'id' => $this->id,
            'name' => $this->name,
            'custom_params' => $this->custom_params,
            'ad_model' => $this->ad_model,
        ];

        if (isset($params['icons']) && $params['icons'])
        {
            $client = new \Spatie\Dropbox\Client(env('DROPBOX_AUTH_TOKEN', ''));
            $icon_url = '';
            if ($this->icon != '' && !is_null($this->icon) && $this->icon != NULL) {
                try {
                    $icon_url = $client->getTemporaryLink($this->icon);
                } catch (\Spatie\Dropbox\Exceptions\BadRequest $e) {
                }
            }
        
            $toReturn['icon'] = $icon_url;
        }

        return $toReturn;
    }
}
