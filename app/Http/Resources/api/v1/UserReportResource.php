<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

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
        // Calculate traffic quality
        $tq = 0;
        $total_cost = $this->cost + $this->extra_cost;
        $roi = 0;
        if ($this->commission > 0) {
            $roi = ($total_cost > 0) ? (($this->commission - $total_cost)*100 / $total_cost) : (9999.99);
        }
        if ($roi <= 0) {
            $tq = 0;
        } elseif ($roi >= 100) {
            $tq = 10;
        } else {
            $tq = intval($roi / 10);
        }
        $toReturn = [
            'id' => $this->id,
            'user_id' => $this->user->id,
            'day' => $this->day,
            'visits' => $this->visits,
            'leads' => $this->leads,
            'cost' => $this->cost,
            'extra_cost' => $this->extra_cost,
            'tq' => $tq,
            'notes' => $this->notes,
        ];

        if (Auth::check() && Auth::user()->hasPermission('form', 'viewCommission')) {
            $toReturn['commission'] = $this->commission;
            $toReturn['profit'] = $this->profit;
        }

        return $toReturn;
    }
}
