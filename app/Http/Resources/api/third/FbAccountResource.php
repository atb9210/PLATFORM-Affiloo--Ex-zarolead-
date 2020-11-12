<?php

namespace App\Http\Resources\api\third;

use Illuminate\Http\Resources\Json\JsonResource;

class FbAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $product = [
            'id' => null,
            'name' => null,
        ];
        if ($this->product_id != null) {
            $product['id'] = $this->product->id;
            $product['name'] = $this->product->name;
        }

        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
            ],
            'name' => $this->name,
            'product' => $product,
            'notes' => $this->notes,
            'token' => $this->token,
            'info' => $this->info,
            'status' => $this->status,
            'token_status' => $this->token_status,
            'profile' => $this->profile,
            'created_at' => $this->created_at,
        ];
    }
}
