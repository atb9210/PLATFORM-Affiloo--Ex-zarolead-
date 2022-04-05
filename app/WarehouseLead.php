<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseLead extends Model
{
    const STATUSES = [
        'unprocessed',
        'to_recall',
        'no_asnwer',
        'fake',
        'cancelled',
        'duplicate',
        'waiting_payment',
        'confirmed',
    ];

    protected $fillable = [
        'hash',
        'customer_id',
        'product_id',
        'status',
        'first_name', 'last_name', 'phone', 'email',
        'address_line1', 'address_line2', 'city', 'postcode', 'zone', 'country',
        'notes', 'info',
        'archived',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id');
    }

    public function warehouseProduct()
    {
        if ($this->product_id === null) {
            return null;
        }

        $product = $this->product;
        $sku = null;
        $info = $product->info;
        if ($info !== null && isset($info['sku']) && $info['sku'] !== null && $info['sku'] != '') {
            $sku = $info['sku'];
        }
        if ($sku === null) {
            return null;
        }

        $warehouseProduct = WarehouseProduct::findBySku($sku);
        if ($warehouseProduct !== null) {
            return $warehouseProduct;
        }

        $variation = WarehouseProductVariation::findBySku($sku);
        if ($variation === null) {
            return null;
        }

        return $variation->product;
    }

    private function generateRandomString(int $length = 32): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function generateHash(): string
    {
        return $this->generateRandomString(64);
    }

    public function refreshHash(): string
    {
        $newHash = null;

        $endLoop = false;
        do {
            $newHash = $this->generateHash();
            if (WarehouseLead::findByHash($newHash) === null) {
                $endLoop = true;
            }
        } while (!$endLoop);

        $this->hash = $newHash;
        $this->save();

        return $this->hash;
    }

    public static function findByHash(string $hash)
    {
        $results = WarehouseLead::where('hash', $hash)->get();
        if ($results->count() > 0) {
            return $results[0];
        }
        return null;
    }
}
