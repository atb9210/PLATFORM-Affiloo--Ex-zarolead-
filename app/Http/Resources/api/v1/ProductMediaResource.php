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
        $pathname = basename(dirname($this->pathname)) . "/" . basename($this->pathname);
        return [
            'id' => $this->id,
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
            ],
            'type' => $this->type,
            'pathname' => $pathname,
            'original_file_name' => $this->original_file_name,
            'url' => $this->public_url,
            'url_preview' => $this->public_url,
            'created_at' => $this->created_at,
        ];
    }
}
