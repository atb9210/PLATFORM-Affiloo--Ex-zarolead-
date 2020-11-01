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
        $hasPreview = ($this->product_media_preview_id != null && $this->product_media_preview != null && $this->product_media_preview->pathname != '');

        $toReturn = [
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
                'url' => $this->product_media->public_url,
                'url_preview' => $this->product_media->public_url,
                'original_file_name' => $this->product_media->original_file_name,
            ],
            'hasPreview' => $hasPreview,
            'created_at' => $this->created_at,
        ];

        if ($hasPreview) {
            $toReturn['product_media_preview'] = [
                'id' => $this->product_media_preview->id,
                'type' => $this->product_media_preview->type,
                'url' => $this->product_media_preview->public_url,
                'url_preview' => $this->product_media_preview->public_url,
                'original_file_name' => $this->product_media_preview->original_file_name,
            ];
        }

        return $toReturn;
    }
}
