<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserReportResource extends JsonResource
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
            'user_id' => $this->user->id,
            'day' => $this->day,
            'visits' => $this->visits,
            'leads' => $this->leads,
            'commission' => $this->commission,
            'cost' => $this->cost,
            'extra_cost' => $this->extra_cost,
            'profit' => $this->profit,
            'notes' => $this->notes,
        ];
    }
}
