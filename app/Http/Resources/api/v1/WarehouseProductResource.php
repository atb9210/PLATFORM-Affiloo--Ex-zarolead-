<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseProductResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $variations = $this->variations;
        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'lines' => WarehouseProductVariationResource($variations),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
