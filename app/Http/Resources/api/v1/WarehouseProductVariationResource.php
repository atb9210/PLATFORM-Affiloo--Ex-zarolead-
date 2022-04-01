<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseProductVariationResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $productName = $this->product->name;
        $fullName = $productName . ' ' . $this->name;
        return [
            'id' => $this->id,
            'warehouse_product_id' => $this->warehouse_product_id,
            'sku' => $this->sku,
            'name' => $this->name,
            'product_name' => $productName,
            'full_name' => $fullName,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
