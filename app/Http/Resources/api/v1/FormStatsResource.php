<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class FormStatsResource extends JsonResource
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
        $roi = 0;
        if ($this->total_commission > 0) {
            $roi = ($this->total_cost > 0) ? (($this->total_commission - $this->total_cost)*100 / $this->total_cost) : (9999.99);
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
            'form' => [
                'id' => $this->form->id,
                'name' => $this->form->name,
            ],
            'day' => $this->day,
            'visits' => $this->visits,
            'leads' => $this->leads,
            'total_cost' => $this->total_cost,
            'tq' => $tq,
            'info' => $this->info,
            'custom_params_stats' => json_decode(json_encode($this->custom_params_stats)),
        ];

        if (Auth::check() && Auth::user()->hasPermission('form', 'viewCommission')) {
            $toReturn['total_commission'] = $this->total_commission;
        }

        return $toReturn;
    }
}
