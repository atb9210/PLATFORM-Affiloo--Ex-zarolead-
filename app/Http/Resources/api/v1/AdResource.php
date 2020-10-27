<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
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
        $file_url = '';
        $file_url_preview = '';
        if ($this->product_media->pathname != '') {
            $file_url = $client->getTemporaryLink($this->product_media->pathname);
        }
        if ($this->product_media->type == 'video' && $this->product_media->pathname_preview != '') {
            try {
                $file_url_preview = $client->getTemporaryLink($this->product_media->pathname_preview);
            } catch (\Spatie\Dropbox\Exceptions\BadRequest $e) {
            }
        } elseif ($this->product_media->type == 'image') {
            $file_url_preview = $file_url;
        }

        return [
            'id' => $this->id,
            'traffic_source' => [
                'id' => $this->traffic_source->id,
                'name' => $this->traffic_source->name,
            ],
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
            ],
            'ctr' => $this->ctr,
            'cpc' => $this->cpc,
            'cpa' => $this->cpa,
            'info' => $this->info,
            'product_media' => [
                'id' => $this->product_media->id,
                'type' => $this->product_media->type,
                'file_url' => $file_url,
                'file_url_preview' => $file_url_preview,
            ],
            'created_at' => $this->created_at,
        ];
    }
}
