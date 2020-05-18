<?php

namespace App\Http\Resources\api\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
            ],
            'uploaded' => ($this->uploaded == 1),
            'transaction' => $this->transaction,
            'language' => $this->language,
            'invoice_date' => $this->invoice_date,
            'transaction_id' => $this->transaction_id,
            'number' => $this->number,
            'status' => $this->status,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'info' => $this->info,
            'original_file_name' => $this->original_file_name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
