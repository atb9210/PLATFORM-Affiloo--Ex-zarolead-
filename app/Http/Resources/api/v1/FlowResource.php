<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class FlowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $domain = $this->domain->domain;
        if ($domain[strlen($domain) - 1] != '/') {
          $domain .= '/';
        }
        $urlDomain = $domain . 'flo/' . $this->path_name;
        if (count($this->traffic_source->custom_params) > 0) {
            if (strpos($urlDomain, '?') === false) {
                $urlDomain .= '?';
            } else {
                $urlDomain .= '&';
            }
            $urlDomain .= $this->traffic_source->getUrlParams();
        }

        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'id' => $this->user->email,
            ],
            'name' => $this->name,
            'type' => $this->type,
            'rules' => $this->rules,
            'traffic_source' => [
                'id' => $this->traffic_source->id,
                'name' => $this->traffic_source->name,
            ],
            'domain' => [
                'id' => $this->domain->id,
                'domain' => $this->domain->domain,
            ],
            'path_name' => $this->path_name,
            'url' => $this->getPathnameUrl(),
            'url_domain' => $urlDomain,
        ];
    }
}
