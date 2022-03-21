<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class WarehouseOrderResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $variation = $this->variation;
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product_variation' => [
                'id' => $variation->id,
                'name' => $variation->id,
                'sku' => $variation->sku,
                'price' => $variation->price,
            ],
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'discount' => $this->discount,
            'final_price' => $this->final_price,
            'attributes' => $this->attributes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
