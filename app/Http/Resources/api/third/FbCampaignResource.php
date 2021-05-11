<?php

namespace App\Http\Resources\api\third;

use Illuminate\Http\Resources\Json\JsonResource;

class FbCampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $form = null;
        if ($this->form_id != null) {
            $form = [
                'id' => $this->form->id,
                'name' => $this->form->name,
            ];
        }

        return [
            'id' => $this->id,
            'form' => $form,
        ];
    }
}
