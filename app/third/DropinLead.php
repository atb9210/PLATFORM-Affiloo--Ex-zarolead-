<?php

namespace App\third;

use Illuminate\Database\Eloquent\Model;

class DropinLead extends Model
{
    protected $table = 'third_dropin_leads';

    protected $fillable = [
        'first_name', 'last_name', 'phone', 'email', 'status', 'info', 'notes', 'hash',
    ];

    protected $casts = [
        'info' => 'array',
    ];

    public static $statuses = [
        'unprocessed',
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

    public static function findByHash(string $hash)
    {
        $results = DropinLead::where('hash', $hash)->get();
        if ($results->count() > 0) {
            return $results[0];
        }
        return null;
    }
}
