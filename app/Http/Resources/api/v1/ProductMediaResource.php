<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductMediaResource extends JsonResource
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
        if ($this->pathname != '') {
            if ($this->public_url == null || $this->public_url == '') {
                try {
                    $sharedLink = $client->createSharedLinkWithSettings($this->pathname);
                    $url = $sharedLink['url'];
                    $this->public_url = $url;
                    $this->save();
                } catch (\Spatie\Dropbox\Exceptions\BadRequest $e) {
                    $sharedLinks = $client->listSharedLinks($this->pathname);
                    $sharedLink = $sharedLinks[0];
                    $url = $sharedLink['url'];
                    $this->public_url = $url;
                    $this->save();
                }
            } else {
                $url = $this->public_url;
            }
            try {
                $url_preview = $client->getTemporaryLink($this->pathname);
            } catch (\Spatie\Dropbox\Exceptions\BadRequest $e) {
            }
        }

        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
            ],
            'type' => $this->type,
            'pathname' => $this->pathname,
            'original_file_name' => $this->original_file_name,
            'url' => $url,
            'url_preview' => $url_preview,
            'created_at' => $this->created_at,
        ];
    }
}
