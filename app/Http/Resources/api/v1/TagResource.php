<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $bg_color = '';
        $fg_color = '';

        if (isset($this->info['bg_color'])) {
            $bg_color = $this->info['bg_color'];
        }
        if (isset($this->info['fg_color'])) {
            $fg_color = $this->info['fg_color'];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'bg_color' => $bg_color,
            'fg_color' => $fg_color,
        ];
    }
}
