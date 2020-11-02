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
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
            ],
            'name' => $this->name,
            'notes' => $this->notes,
            'token' => $this->token,
            'status' => $this->status,
            'token_status' => $this->token_status,
            'profile' => $this->profile,
            'created_at' => $this->created_at,
        ];
    }
}
