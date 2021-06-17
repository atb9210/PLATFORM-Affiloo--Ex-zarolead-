<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Domain;

class DomainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $domain = Domain::findOrFail($this->id);

        $info = [];
        if ($domain->info !== null) {
            $info = $domain->info;
        }
        if (!isset($domain->info['status'])) {
            $info['status'] = (\App\Domain::isOnline($domain->domain)) ? 'online' : 'offline';
        }
        $domain->info = $info;
        $domain->save();

        return [
            'id' => $this->id,
            'domain' => $this->domain,
            'path' => $this->path,
            'info' => $this->info,
            'status' => $domain->info['status'],
            'ssl' => $this->getSSL(),
        ];
    }
}
