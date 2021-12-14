<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class WarehouseLead extends Model
{
    protected $table = 'third_warehouse_leads';

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'status', 'info', 'notes', 'hash', 'created_at',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public static $statuses = [
        'confirmed',
        'no answer',
        'cancelled',
        'fake',
        'duplicate',
        'to recall',
        'to do',
        'waiting payment',
        'abandoned',
    ];

    public static function findByHash(string $hash): WarehouseLead|null
    {
        $results = WarehouseLead::where('hash', $hash)->get();
        if ($results->count() > 0) {
            return $results[0];
        }
        return null;
    }
}
