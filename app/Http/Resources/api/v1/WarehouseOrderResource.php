<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseOrderResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $customer = $this->customer;
        $lines = $this->lines;
        return [
            'id' => $this->id,
            'status' => $this->status,
            'amount' => $this->amount,
            'customer' => [
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address_line1' => $customer->address_line1,
                'address_line2' => $customer->address_line2,
                'city' => $customer->city,
                'postcode' => $customer->postcode,
                'zone' => $customer->zone,
                'country' => $customer->country,
            ],
            'lines' => WarehouseOrderLineResource($lines),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
