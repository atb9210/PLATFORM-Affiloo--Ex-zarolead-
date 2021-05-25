<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

use App\LandingStats as LandingStats;

class LandingResource extends JsonResource
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
        $stats = LandingStats::where('landing_id', $this->id);
        if (isset($params['filter_date_from'])) {
            $date_from = $params['filter_date_from'];
            $stats = $stats->where('day', '>=', $params['filter_date_from']);
        }
        if (isset($params['filter_date_to'])) {
            $date_to = $params['filter_date_to'];
            $stats = $stats->where('day', '<=', $params['filter_date_to']);
        }
        $stats = $stats->orderBy('day', 'desc');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
            'user' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
            ],
            'product' => [
                'id' => $this->product->id,
                'name' => $this->product->name,
                'tag' => [
                    'id' => $this->product->tag->id,
                    'name' => $this->product->tag->name,
                ],
            ],
            'tag' => [
                'id' => $this->product->tag->id,
                'name' => $this->product->tag->name,
            ],
            'url' => $this->url,
            'country' => $this->country,
            'daily_stats' => $stats->get(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
