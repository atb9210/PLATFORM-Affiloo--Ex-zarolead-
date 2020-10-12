<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvertiserReportResource extends JsonResource
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
                'name' => $this->user->name,
            ],
            'month' => $this->month,
            'year' => $this->year,
            'visits' => $this->visits,
            'leads' => $this->leads,
            'commission' => $this->commission,
            'approved_commission' => $this->approved_commission,
            'total_cost' => $this->total_cost,
            'profit' => $this->profit,
        ];
    }
}
