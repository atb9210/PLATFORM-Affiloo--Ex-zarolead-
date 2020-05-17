<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class FormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
            ],
            'network' => $this->network->name,
            'product' => $this->product->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'commission' => $this->commission,
            'sub_commission' => $this->sub_commission,
            'total_commission' => $this->total_commission,
            'traffic_source' => $this->traffic_source->name,
            'info' => $this->info,
            'visits' => $this->visits,
            'leads' => $this->leads,
            'total_cost' => $this->total_cost,
            'created_at' => $this->created_at,
        ];
    }
}
