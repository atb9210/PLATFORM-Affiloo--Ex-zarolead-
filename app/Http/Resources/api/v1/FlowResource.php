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
        $domain = $this->getPathnameUrl();
        if (isset($this->domains['list']) && count($this->domains['list']) > 0) {
            $i = 0;
            if (isset($this->domains['selected']) && $this->domains['selected'] > 0 && $this->domains['selected'] < count($this->domains['list'])) {
                $i = intval($this->domains['selected']);
            }
            $domain = $this->domains['list'][$i];
        }
        $urlDomain = $domain;
        $tokens = [];
        foreach ($this->traffic_source->custom_params as $param) {
            $tokens[$param['query_param']] = $param['token'];
        }
        $isFirst = true;
        if (count($tokens) > 0 && (strpos($urlDomain, '?') === false)) {
            $urlDomain .= '?';
        } else {
            $isFirst = false;
        }
        foreach ($tokens as $key => $value) {
            if (!$isFirst) {
                $urlDomain .= '&';
            } else {
                $isFirst = false;
            }
            $urlDomain .= $key . '=' . $value;
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
            'domains' => $this->domains,
            'path_name' => $this->path_name,
            'url' => $this->getPathnameUrl(),
            'url_domain' => $urlDomain,
        ];
    }
}
