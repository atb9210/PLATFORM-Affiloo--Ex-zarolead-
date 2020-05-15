<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class OrderResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        $commission = $this->sub_commission;
        if (Auth::user()->hasPermission('order', 'viewCommission')) {
            $commission = $this->commission;
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'phone' => $this->phone,
            'product' => $this->product->name,
            'commission' => $commission,
            'quantity' => $this->quantity,
            'network' => $this->network->name,
            'status' => $this->status->description,
            'info' => $this->info,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
