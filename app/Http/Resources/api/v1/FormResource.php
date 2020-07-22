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
        // Param validation
        $params = $request->validate([
            'filter_date_from' => 'nullable|date|date_format:Y-m-d',
            'filter_date_to' =>   'nullable|date|date_format:Y-m-d',
        ]);

        $date_from = '';
        $date_to = '';
        if (isset($params['filter_date_from'])) {
            $date_from = $params['filter_date_from'];
        }
        if (isset($params['filter_date_to'])) {
            $date_to = $params['filter_date_to'];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'user' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
            ],
            'campaign' => [
                'id' => $this->campaign->id,
                'name' => $this->campaign->name,
            ],
            'network' => [
                'id' => $this->network->id,
                'name' => $this->network->name,
            ],
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'tag' => [
                    'id' => $this->product->tag->id,
                    'name' => $this->product->tag->name,
                ],
            ],
            'quantity' => json_decode(json_encode($this->quantity), false),
            'upsell' => $this->upsell,
            'total_commission' => $this->total_commission,
            'traffic_source' => [
                'id' => $this->traffic_source->id,
                'name' => $this->traffic_source->name,
            ],
            'info' => $this->info,
            'network_params' => $this->network_params,
            'plugins' => $this->plugins,
            'visits' => $this->visits,
            'leads' => $this->leads,
            'total_cost' => $this->total_cost,
            'stats' => $this->getStats($date_from, $date_to),
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'archived' => $this->archived,
        ];
    }
}
