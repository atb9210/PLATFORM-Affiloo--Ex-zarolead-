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
        $file_url = '';
        $file_url_preview = '';
        if ($this->pathname != '') {
            $file_url = $client->getTemporaryLink($this->pathname);
        }
        if ($this->type == 'video' && $this->pathname_preview != '') {
            try {
                $file_url_preview = $client->getTemporaryLink($this->pathname_preview);
            } catch (\Spatie\Dropbox\Exceptions\BadRequest $e) {
            }
        } elseif ($this->type == 'image') {
            $file_url_preview = $file_url;
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
            'pathname_preview' => $this->pathname_preview,
            'original_file_name_preview' => $this->original_file_name_preview,
            'file_url' => $file_url,
            'file_url_preview' => $file_url_preview,
            'created_at' => $this->created_at,
        ];
    }
}
