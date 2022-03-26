<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseShippingResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $carrier = $this->carrier;
        return [
            'id' => $this->id,
            'order_id' => $this->warehouse_order_id,
            'carrier' => [
                'id' => $carrier->id,
                'name' => $carrier->name,
                'slug' => $carrier->slug,
            ],
            'status' => $this->status,
            'tracking_number' => $this->tracking_number,
            'tracking_url' => $this->tracking_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'delivered_at' => $this->delivered_at,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'zone' => $this->zone,
            'country' => $this->country,
            'notes' => $this->notes,
            'info' => $this->info,
        ];
    }
}
