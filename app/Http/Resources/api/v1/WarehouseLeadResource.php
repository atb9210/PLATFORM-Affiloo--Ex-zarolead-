<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseLeadResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $customer = $this->customer;
        if ($customer !== null) {
            $customer = new CustomerResource($customer);
        }

        $product = null;
        if ($this->product_id !== null) {
            $p = $this->product;
            $sku = null;
            $info = $p->info;
            if ($info === null) {
                $info = [];
            }
            if (isset($info['sku']) && $info['sku'] !== null && $info['sku'] != '') {
                $sku = $info['sku'];
            }
            $product = [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $info['sku'],
            ];
        }

        return [
            'id' => $this->id,
            'hash' => $this->hash,
            'customer' => $customer,
            'product' => $product,
            'status' => $this->status,
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
