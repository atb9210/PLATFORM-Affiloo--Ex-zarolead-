<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class LandingStatsResource extends JsonResource
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
            'landing' => [
                'id' => $this->landing->id,
                'name' => $this->landing->name,
            ],
            'day' => $this->day,
            'visits' => $this->visits,
            'leads' => $this->leads,
            'total_commission' => $this->total_commission,
            'total_cost' => $this->total_cost,
        ];
    }
}
