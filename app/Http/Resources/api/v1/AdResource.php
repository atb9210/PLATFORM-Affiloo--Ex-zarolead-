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
        $url = '';
        $url_preview = '';
        if ($this->product_media->pathname != '') {
            if ($this->product_media->public_url == null || $this->product_media->public_url == '') {
                try {
                    $sharedLink = $client->createSharedLinkWithSettings($this->product_media->pathname);
                    $url = $sharedLink['url'];
                    $this->product_media->public_url = $url;
                    $this->product_media->save();
                } catch (\Spatie\Dropbox\Exceptions\BadRequest $e) {
                    $sharedLinks = $client->listSharedLinks($this->product_media->pathname);
                    $sharedLink = $sharedLinks[0];
                    $url = $sharedLink['url'];
                    $this->product_media->public_url = $url;
                    $this->product_media->save();
                }
            } else {
                $url = $this->product_media->public_url;
            }
            try {
                $url_preview = $client->getTemporaryLink($this->product_media->pathname);
            } catch (\Spatie\Dropbox\Exceptions\BadRequest $e) {
            }
        }

        $hasPreview = false;
        $preview_url = '';
        $preview_url_preview = '';
        if ($this->product_media_preview_id != null && $this->product_media_preview != null && $this->product_media_preview->pathname != '') {
            $hasPreview = true;
            if ($this->product_media_preview->public_url == null || $this->product_media_preview->public_url == '') {
                try {
                    $sharedLink = $client->createSharedLinkWithSettings($this->product_media_preview->pathname);
                    $preview_url = $sharedLink['url'];
                    $this->product_media_preview->public_url = $preview_url;
                    $this->product_media_preview->save();
                } catch (\Spatie\Dropbox\Exceptions\BadRequest $e) {
                    $sharedLinks = $client->listSharedLinks($this->product_media_preview->pathname);
                    $sharedLink = $sharedLinks[0];
                    $preview_url = $sharedLink['url'];
                    $this->product_media_preview->public_url = $preview_url;
                    $this->product_media_preview->save();
                }
            } else {
                $preview_url = $this->product_media_preview->public_url;
            }
            try {
                $preview_url_preview = $client->getTemporaryLink($this->product_media_preview->pathname);
            } catch (\Spatie\Dropbox\Exceptions\BadRequest $e) {
            }
        }

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
                'url' => $url,
                'url_preview' => $url_preview,
            ],
            'hasPreview' => $hasPreview,
            'created_at' => $this->created_at,
        ];

        if ($hasPreview) {
            $toReturn['product_media_preview'] = [
                'id' => $this->product_media_preview->id,
                'type' => $this->product_media_preview->type,
                'url' => $preview_url,
                'url_preview' => $preview_url_preview,
            ];
        }

        return $toReturn;
    }
}
