<?php

namespace App\Http\Resources\api\third;

use Illuminate\Http\Resources\Json\JsonResource;

class FacebookMonitorResource extends JsonResource
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
            'reports'          => 'boolean',
            'filter_date_from' => 'date|date_format:Y-m-d',
            'filter_date_to'   => 'date|date_format:Y-m-d',
        ]);

        $toReturn = [
            'id' => $this->id,
            'name' => $this->name,
            'advertiser' => [
                'id' => $this->advertiser->id,
                'name' => $this->advertiser->name,
            ],
            'form' => [
                'id' => $this->form->id,
                'name' => $this->form->name,
            ],
            'campaigns' => $this->campaigns,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if (isset($params['reports']) && boolval($params['reports'])) {
            $fromDate = '';
            $toDate = '';
            if (isset($params['filter_date_from'])) {
                $fromDate = $params['filter_date_from'];
            }
            if (isset($params['filter_date_to'])) {
                $toDate = $params['filter_date_to'];
            }
            $toReturn['reports'] = $this->getReports($fromDate, $toDate);
        }

        return $toReturn;
    }
}
